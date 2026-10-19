<?php

namespace App\Imports;

use App\Models\Manufacturer;
use App\Models\ProductCategory;
use App\Models\ProductUnit;
use App\Models\Stock;
use App\Models\Supplier;
use App\Services\InventoryQuantityService;
use App\Services\MultiLevelStockSheet;
use App\Services\StockUnitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Imports the template produced by MultiLevelStockExport for multi-level stores.
 * Each row creates a product, its unit chain (via StockUnitService::saveUnits) and,
 * when quantities are supplied, an opening stock batch stored as base pieces.
 */
class ImportNewMultiLevelStock implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['name']) || trim((string)$row['name']) === '') {
            return null;
        }

        $name = trim((string)$row['name']);

        DB::transaction(function () use ($row, $name) {
            $stock = new Stock([
                'name' => $name,
                'selling_price' => 0,
                'cost_price' => 0,
                'yard_selling_price' => 0,
                'yard_cost_price' => 0,
                'type' => $this->productType($row),
            ]);

            $categoryId = $this->resolve(ProductCategory::class, $row['category'] ?? null);
            if ($categoryId) {
                $stock->product_category_id = $categoryId;
            }
            $manufacturerId = $this->resolve(Manufacturer::class, $row['manufacturer'] ?? null);
            if ($manufacturerId) {
                $stock->manufacturer_id = $manufacturerId;
            }
            $stock->save();

            [$input, $quantities, $errors] = $this->readUnits($row);
            if (!$errors) {
                $errors = StockUnitService::saveUnits($stock, $input);
            }
            if ($errors) {
                throw ValidationException::withMessages([
                    'excel_file' => ["{$name}: " . implode(' ', $errors)],
                ]);
            }

            $this->receiveOpeningStock($stock, $row, $quantities, $name);
        });

        return null;
    }

    private function productType(array $row): string
    {
        $type = strtoupper(trim((string)($row['product_type'] ?? '')));
        return ($type === '' || $type === 'SINGLE') ? 'NORMAL' : $type;
    }

    /** Find a category/manufacturer by name or id, creating it from a new name. */
    private function resolve(string $model, $value)
    {
        $value = trim((string)$value);
        if ($value === '' || strtoupper($value) === 'N/A') {
            return null;
        }
        $record = $model::where('name', $value)->first();
        if (!$record && is_numeric($value)) {
            $record = $model::find($value);
        }
        if (!$record) {
            $record = $model::create(['name' => $value, 'status' => 1]);
        }
        return $record->id;
    }

    private function filled($value): bool
    {
        return $value !== null && trim((string)$value) !== '';
    }

    /** A cell that carries information: not blank and not just a zero. */
    private function meaningful($value): bool
    {
        return $this->filled($value) && !(is_numeric($value) && (float)$value == 0.0);
    }

    /**
     * Turn the row's unit columns into [saveUnits input, quantities by level, errors].
     *
     * Packaging units (Cartons, Packets...) are optional per product:
     *  - a packaging unit whose cells are all blank/zero is skipped, so single products can be
     *    imported with only the base unit (Pieces) filled;
     *  - a packaging unit needs a conversion (its "per parent" cell) unless it is the first/largest
     *    unit the product uses; if the conversion is missing and nothing else is entered for it
     *    (no quantity), it is skipped instead of failing the import;
     *  - a packaging unit that has an opening quantity but no conversion is a real error, because
     *    the quantity cannot be turned into pieces.
     */
    private function readUnits(array $row): array
    {
        $input = [];
        $quantities = [];
        $errors = [];
        $types = [MultiLevelStockSheet::COST, MultiLevelStockSheet::SELLING, MultiLevelStockSheet::QTY, MultiLevelStockSheet::PER_PARENT];

        // 1. gather candidate levels
        $candidates = [];
        foreach (MultiLevelStockSheet::levels() as $level) {
            $cols = $level['columns'];
            $cells = [];
            foreach ($types as $type) {
                $cells[$type] = isset($cols[$type]) ? ($row[$cols[$type]['key']] ?? null) : null;
                if ($this->filled($cells[$type]) && !is_numeric($cells[$type])) {
                    $errors[] = "{$cols[$type]['heading']} must be a number.";
                }
            }
            $used = $level['is_base'];
            foreach ($types as $type) {
                if ($this->meaningful($cells[$type])) {
                    $used = true;
                }
            }
            if ($used) {
                $candidates[] = ['level' => $level, 'cells' => $cells];
            }
        }
        if ($errors) {
            return [[], [], array_values(array_unique($errors))];
        }

        // 2. keep packaging units that can actually be converted
        $kept = [];
        foreach ($candidates as $cand) {
            $level = $cand['level'];
            $cells = $cand['cells'];
            $isFirst = count($kept) === 0;
            $hasConversion = $this->meaningful($cells[MultiLevelStockSheet::PER_PARENT]);

            if (!$level['is_base'] && !$isFirst && !$hasConversion) {
                if ($this->meaningful($cells[MultiLevelStockSheet::QTY])) {
                    $errors[] = "{$level['name']} has a quantity but no '{$level['columns'][MultiLevelStockSheet::PER_PARENT]['heading']}'.";
                }
                continue; // not used for this product
            }
            $kept[] = $cand;
        }

        // 3. build the saveUnits input
        foreach ($kept as $idx => $cand) {
            $level = $cand['level'];
            $cells = $cand['cells'];
            $cols = $level['columns'];

            if ($level['is_base'] && $idx > 0 && !$this->meaningful($cells[MultiLevelStockSheet::PER_PARENT])) {
                $errors[] = "{$cols[MultiLevelStockSheet::PER_PARENT]['heading']} is required when the product uses cartons or packets.";
            }

            $input[$level['level']] = [
                'name' => $level['name'],
                'units_per_parent' => $idx > 0 ? $cells[MultiLevelStockSheet::PER_PARENT] : null,
                'cost_price' => $this->filled($cells[MultiLevelStockSheet::COST]) ? $cells[MultiLevelStockSheet::COST] : 0,
                'selling_price' => $this->filled($cells[MultiLevelStockSheet::SELLING]) ? $cells[MultiLevelStockSheet::SELLING] : 0,
            ];

            $qty = $cells[MultiLevelStockSheet::QTY];
            if ($this->filled($qty)) {
                if ((float)$qty < 0) {
                    $errors[] = "{$cols[MultiLevelStockSheet::QTY]['heading']} cannot be negative.";
                } elseif ((float)$qty > 0) {
                    $quantities[$level['level']] = (float)$qty;
                }
            }
        }

        return [$input, $quantities, array_values(array_unique($errors))];
    }

    private function receiveOpeningStock(Stock $stock, array $row, array $quantities, string $name): void
    {
        if (!$quantities) {
            return;
        }

        $units = ProductUnit::where('stock_id', $stock->id)->where('status', 1)->get()->keyBy('level');
        $received = [];
        foreach ($quantities as $level => $qty) {
            $received[$units[$level]->id] = $qty;
        }
        $base = InventoryQuantityService::toBaseMixed(InventoryQuantityService::normalize($units->values()), $received);
        if ($base <= 0) {
            return;
        }

        $store = getActiveStore();
        $stock->stockbatches()->create([
            'received_date' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplierId($row),
            $store->yard_column => $base,
        ]);
    }

    private function supplierId(array $row)
    {
        $supplierName = trim((string)($row['supplier_name'] ?? ''));
        if ($supplierName === '') {
            return null;
        }
        $supplier = Supplier::where('name', $supplierName)->first();
        if (!$supplier && is_numeric($supplierName)) {
            $supplier = Supplier::find($supplierName);
        }
        if (!$supplier) {
            $phone = trim((string)($row['supplier_phone'] ?? ''));
            $supplier = Supplier::create([
                'name' => $supplierName,
                'phonenumber' => $phone !== '' ? $phone : $supplierName,
            ]);
        }
        return $supplier->id;
    }
}

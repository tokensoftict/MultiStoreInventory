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

    /**
     * Turn the row's unit columns into [saveUnits input, quantities by level, errors].
     * The base unit is always used; a packaging unit is used when any of its cells is filled.
     */
    private function readUnits(array $row): array
    {
        $input = [];
        $quantities = [];
        $errors = [];

        foreach (MultiLevelStockSheet::levels() as $level) {
            $cols = $level['columns'];
            $cell = function ($type) use ($row, $cols) {
                return isset($cols[$type]) ? ($row[$cols[$type]['key']] ?? null) : null;
            };

            $used = $level['is_base'];
            foreach ([MultiLevelStockSheet::COST, MultiLevelStockSheet::SELLING, MultiLevelStockSheet::QTY, MultiLevelStockSheet::PER_PARENT] as $type) {
                if ($this->filled($cell($type))) {
                    $used = true;
                }
            }
            if (!$used) {
                continue;
            }

            foreach ([MultiLevelStockSheet::COST, MultiLevelStockSheet::SELLING, MultiLevelStockSheet::QTY, MultiLevelStockSheet::PER_PARENT] as $type) {
                $v = $cell($type);
                if ($this->filled($v) && !is_numeric($v)) {
                    $errors[] = "{$cols[$type]['heading']} must be a number.";
                }
            }

            $input[$level['level']] = [
                'name' => $level['name'],
                'units_per_parent' => $cell(MultiLevelStockSheet::PER_PARENT),
                'cost_price' => $this->filled($cell(MultiLevelStockSheet::COST)) ? $cell(MultiLevelStockSheet::COST) : 0,
                'selling_price' => $this->filled($cell(MultiLevelStockSheet::SELLING)) ? $cell(MultiLevelStockSheet::SELLING) : 0,
            ];

            $qty = $cell(MultiLevelStockSheet::QTY);
            if ($this->filled($qty) && is_numeric($qty)) {
                if ($qty < 0) {
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

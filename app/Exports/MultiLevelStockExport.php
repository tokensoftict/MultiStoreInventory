<?php

namespace App\Exports;

use App\Models\ProductUnit;
use App\Services\InventoryQuantityService;
use App\Services\MultiLevelStockSheet;
use App\Services\StockUnitService;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Stocks sheet for stores using the multi-level (Carton -> Packet -> Piece) quantity system.
 * With ?template=true it yields an empty, importable template; otherwise the current stock
 * broken down per unit.
 */
class MultiLevelStockExport implements FromCollection, WithHeadings, WithTitle
{
    protected function isTemplate(): bool
    {
        return \request()->has('template');
    }

    public function collection()
    {
        if ($this->isTemplate()) {
            return collect([]);
        }

        $store = getActiveStore();
        $levels = MultiLevelStockSheet::levels();

        $stocks = DB::table('stocks')
            ->select(
                'stocks.id',
                'stocks.name as product_name',
                DB::raw('(CASE WHEN stocks.type = "NORMAL" THEN "SINGLE" ELSE stocks.type END) AS type'),
                'product_category.name as category_name',
                'manufacturers.name as manufacturer_name',
                DB::raw('COALESCE(SUM(stockbatches.' . $store->yard_column . '), 0) as base_quantity')
            )
            ->leftJoin('stockbatches', 'stocks.id', '=', 'stockbatches.stock_id')
            ->leftJoin('manufacturers', 'stocks.manufacturer_id', '=', 'manufacturers.id')
            ->leftJoin('product_category', 'stocks.product_category_id', '=', 'product_category.id')
            ->where('stocks.status', 1)
            ->where('stocks.warehousestore_id', $store->id)
            ->groupBy('stocks.id', 'stocks.name', 'stocks.type', 'product_category.name', 'manufacturers.name')
            ->get();

        $productUnits = ProductUnit::whereIn('stock_id', $stocks->pluck('id'))
            ->where('status', 1)
            ->get()
            ->groupBy('stock_id');

        return $stocks->map(function ($stock) use ($levels, $productUnits) {
            $units = ($productUnits[$stock->id] ?? collect())->keyBy('level');
            $breakdown = [];
            if ($units->isNotEmpty()) {
                $normalized = InventoryQuantityService::normalize($units->values());
                foreach (InventoryQuantityService::breakdown($normalized, $stock->base_quantity) as $row) {
                    $breakdown[$row['level']] = $row['quantity'];
                }
            }

            $row = [$stock->id, $stock->product_name, $stock->type, $stock->category_name, $stock->manufacturer_name];
            foreach ($levels as $level) {
                $unit = $units[$level['level']] ?? null;
                $row[] = $unit ? $unit->cost_price : null;
                $row[] = $unit ? $unit->selling_price : null;
                $row[] = $unit ? ($breakdown[$level['level']] ?? 0) : null;
                if (isset($level['columns'][MultiLevelStockSheet::PER_PARENT])) {
                    $row[] = $unit ? $unit->units_per_parent : null;
                }
            }
            return $row;
        });
    }

    public function headings(): array
    {
        return MultiLevelStockSheet::headings($this->isTemplate());
    }

    public function title(): string
    {
        return "Stocks";
    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CurrentStockExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $packed_column = getActiveStore()->packed_column;
        $yard_column = getActiveStore()->yard_column;

            return DB::table('stocks')->select(
                'stocks.id',
                'stocks.name as product_name',
                'stocks.selling_price',
                'stocks.cost_price',
                'stocks.yard_selling_price',
                'stocks.yard_cost_price',
                'stocks.type',
                'product_category.name as category_name',
                'manufacturers.name as manufacturer_name',
                DB::raw('SUM(stockbatches.'.$packed_column.') as bundle_quantity'),
                DB::raw('SUM(stockbatches.'.$yard_column.') as yard_quantity')
            )
                ->leftJoin('stockbatches','stocks.id','=','stockbatches.stock_id')
                ->leftJoin('manufacturers','stocks.manufacturer_id','=','manufacturers.id')
                ->leftJoin('product_category','stocks.product_category_id','=','product_category.id')
                ->where('stocks.status',1)
                ->groupBy('stocks.id')
                ->groupBy('stocks.name')
                ->groupBy('stocks.selling_price')
                ->groupBy('stocks.cost_price')
                ->groupBy('stocks.yard_selling_price')
                ->groupBy('stocks.yard_cost_price')
                ->groupBy('stocks.type')
                ->groupBy('product_category.name')
                ->groupBy('manufacturers.name')
                ->get();




    }

    /**
     * @return array
     */
    public function headings(): array
    {

            return [
                'ID',
                'NAME',
                'SELLING PRICE',
                'COST PRICE',
                'YARD SELLING PRICE',
                'YARD COST PRICE',
                'PRODUCT TYPE',
                'CATEGORY',
                'MANUFACTURER',
                'BUNDLE QUANTITY',
                'YARD QUANTITY',
            ];

    }
}

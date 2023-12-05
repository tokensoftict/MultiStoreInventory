<?php

namespace App\Exports;

use App\Models\Stock;
use App\Models\Stockbatch;
use App\Models\Warehousestore;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromCollection, WithHeadings
{


    protected $stockTaking;

    public function __construct($stockTaking)
    {
        $this->stockTaking = $stockTaking;
    }

        /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $store = Warehousestore::find($this->stockTaking->warehousestore_id);

        $packed_column = $store->packed_column;
        $yard_column = $store->yard_column;

        $stocks =  DB::table('stockbatches')->select(
            'stocks.id',
            'stocks.name',
            //DB::raw('SUM(stockbatches.' . $packed_column . ') as bundle_quantity'),
            //DB::raw('SUM(stockbatches.' . $yard_column . ') as yard_quantity'),
            'product_category.name as cat_name',
        )
            ->join('stocks', 'stocks.id', '=', 'stockbatches.stock_id')
            ->join('product_category', 'stocks.product_category_id','=','product_category.id')
            ->where('stocks.status', 1)
            ->groupBy('stocks.id')
            ->groupBy('stocks.name');

        if(request()->has('categories')){
            if(in_array("all",  request()->get('categories'))) {
            }else{
                $stocks->whereIn('stocks.product_category_id', request()->get('categories'));
            }
        }

        return $stocks->get();

    }

    /**
     * @return array
     */
    public function headings(): array
    {

        return [
            'ID',
            'NAME',
            //'BUNDLE QUANTITY',
            //'YARD QUANTITY',
            'CATEGORY',
            'COUNTED BUNDLE QUANTITY',
            'COUNTED YARD QUANTITY',
        ];



    }
}

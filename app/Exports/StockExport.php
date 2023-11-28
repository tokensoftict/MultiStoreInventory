<?php

namespace App\Exports;

use App\Models\Stock;
use App\Models\Stockbatch;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $packed_column = getActiveStore()->packed_column;
        $yard_column = getActiveStore()->yard_column;

            return DB::table('stockbatches')->select(
                'stocks.id',
                'stocks.name',
                DB::raw('SUM(stockbatches.' . $packed_column . ') as bundle_quantity'),
                DB::raw('SUM(stockbatches.' . $yard_column . ') as yard_quantity'),
            )->join('stocks', 'stocks.id', '=', 'stockbatches.stock_id')
                ->where('stocks.status', 1)
                ->groupBy('stocks.id')
                ->groupBy('stocks.name')
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
                'BUNDLE QUANTITY',
                'YARD QUANTITY',
                'COUNTED BUNDLE QUANTITY',
                'COUNTED YARD QUANTITY',
            ];



    }
}

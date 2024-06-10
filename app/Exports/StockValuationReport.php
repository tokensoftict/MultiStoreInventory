<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class StockValuationReport implements FromCollection
{
    public  $data;


    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->data);
    }
}

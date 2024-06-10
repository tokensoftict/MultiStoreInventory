<?php

namespace App\Exports\Essentials;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;


class StockExportEssentials implements FromCollection, WithHeadings, WithTitle
{
    public  $headings;
    public $data;
    public $title;
    public function __construct(array $headings, $data, $title)
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->title = $title;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}

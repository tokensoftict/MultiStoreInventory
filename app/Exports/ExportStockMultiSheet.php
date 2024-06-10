<?php

namespace App\Exports;

use App\Exports\Essentials\StockExportEssentials;
use App\Models\Manufacturer;
use App\Models\ProductCategory;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportStockMultiSheet  implements WithMultipleSheets
{

    public function sheets(): array
    {
        $headings = ['ID', "NAME"];

        $manufacturer = Manufacturer::select('id', 'name')->get();
        $suppliers = Supplier::select("id", "name")->get();
        $category = ProductCategory::select("id", "name")->get();
        $_productTypes = config('stock_type.'.config('app.store'));
        $productTypes = [];
        foreach ($_productTypes as $key => $productType){
            $productTypes[] = ['id' => $productType, "name" => $key];
        }

        $productTypes = collect($productTypes);

        return [
           "Stocks" =>  new CurrentStockExport(),
           "Manufacturer" => new StockExportEssentials($headings, $manufacturer, "Manufacturer"),
           "Suppliers" => new StockExportEssentials($headings, $suppliers, "Suppliers"),
           "Product Category" => new StockExportEssentials($headings, $category, "Product Category"),
           "Product Types" => new StockExportEssentials($headings, $productTypes, "Product Types"),
        ];

    }
}

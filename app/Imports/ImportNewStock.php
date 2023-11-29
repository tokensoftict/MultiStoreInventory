<?php

namespace App\Imports;

use App\Models\Manufacturer;
use App\Models\ProductCategory;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportNewStock  implements ToModel,WithHeadingRow
{

    public function model(array $row)
    {

        $stock = [];

        if(!isset($row['name'])) return NULL;

        $stock['name'] = $row['name'];

        if(isset($row['category']) && !empty($row['category'])  && $row['category'] != "N/A")
        {
            $category =  ProductCategory::where('name', $row['category'])->get()->first();

            if ($category)
            {
                $stock['product_category_id'] = $category->id;
            }
            else
            {
                $pc = ProductCategory::create(['name' => $row['category'], 'status' => 1]);
                $stock['product_category_id'] = $pc->id;
            }

        }


        if(isset($row['manufacturer']) && !empty($row['manufacturer']) &&  $row['manufacturer'] != "N/A")
        {
            $manufacturer = Manufacturer::where('name', $row['manufacturer'])->get()->first();
            if ($manufacturer) {
                $stock['manufacturer_id'] = $manufacturer->id;
            } else {
                $mn = Manufacturer::create(['name' => $row['manufacturer'], 'status' => 1]);
                $stock['manufacturer_id'] = $mn->id;
            }
        }

        $stock['selling_price'] = empty($row['selling_price']) ? 0 : $row['selling_price'];

        $stock['cost_price'] =empty( $row['cost_price']) ? 0 :  $row['cost_price'];

        $stock['yard_selling_price'] = (empty($row['yard_selling_price']) ? 0 : $row['yard_selling_price']);

        $stock['yard_cost_price'] = (empty($row['yard_cost_price']) ? 0 : $row['yard_cost_price']);

        $stock['type'] = "NORMAL";//empty($row['product_type']) ? "NORMAL" : strtoupper($row['product_type']);

        return new Stock($stock);
    }


}

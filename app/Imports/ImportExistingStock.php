<?php

namespace App\Imports;

use App\Models\Manufacturer;
use App\Models\ProductCategory;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportExistingStock implements ToCollection,WithHeadingRow
{

    public function collection(Collection $collections)
    {
        foreach ($collections as $row)
        {
            if(!isset($row['id'])) continue;

            $stock = Stock::find($row['id']);

            if(isset($row['name'])  && !empty($row['name']))
            {
                $stock->name = $row['name'];
            }


            if(isset($row['category']) && !empty($row['category'])  && $row['category'] != "N/A")
            {
                $category =  ProductCategory::where('name', $row['category'])->get()->first();

                if ($category)
                {
                    $stock->product_category_id = $category->id;
                }
                else
                {
                    $pc = ProductCategory::create(['name' => $row['category'], 'status' => 1]);
                    $stock->product_category_id = $pc->id;
                }

            }


            if(isset($row['manufacturer']) && !empty($row['manufacturer']) &&  $row['manufacturer'] != "N/A")
            {
                $manufacturer = Manufacturer::where('name', $row['manufacturer'])->get()->first();
                if ($manufacturer) {
                    $stock->manufacturer_id = $manufacturer->id;
                } else {
                    $mn = Manufacturer::create(['name' => $row['manufacturer'], 'status' => 1]);
                    $stock->manufacturer_id = $mn->id;
                }
            }

            if(!empty($row['selling_price']))
            {
                $stock->selling_price = $row['selling_price'];
            }

            if(!empty($row['cost_price']))
            {
                $stock->cost_price = $row['cost_price'];
            }


            if(!empty($row['yard_selling_price']))
            {
                $stock->yard_selling_price = $row['yard_selling_price'];
            }


            if(!empty($row['yard_cost_price']))
            {
                $stock->yard_cost_price = $row['yard_cost_price'];
            }

            if(!empty($row['type']))
            {
                $stock->type = strtoupper($row['product_type']) == "SINGLE" ? "NORMAL" : strtoupper($row['product_type']);
            }

            $stock->update();

        }
    }


}

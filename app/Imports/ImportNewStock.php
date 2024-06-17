<?php

namespace App\Imports;

use App\Models\Manufacturer;
use App\Models\ProductCategory;
use App\Models\Stock;
use App\Models\Stockbatch;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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

            if(!$category){
                $category = ProductCategory::find( $row['category']);
            }

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

            if(!$manufacturer){
                $manufacturer = Manufacturer::find($row['manufacturer']);
            }

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

        //$stock['type'] = "NORMAL";//empty($row['product_type']) ? "NORMAL" : strtoupper($row['product_type']);

        $stock['type'] = strtoupper($row['product_type']) == "SINGLE" ? "NORMAL" : strtoupper($row['product_type']);

        $newStock = new Stock($stock);
        $newStock->save();
        if(Arr::has($row, ["bundle_quantity", "yard_quantity", "supplier_name"])){

            $supplier = Supplier::where("name", $row['supplier_name'])->first();

            if(!$supplier){
                $supplier = Supplier::find($row['supplier_name']);
            }


            if(!$supplier){
                $supplier = Supplier::create([
                    'name' => $row['supplier_name'],
                    'phonenumber' =>  $row['supplier_name']
                ]);
            }

            $store = getActiveStore();

            $stockBatch = new Stockbatch([
                'quantity' => $row['bundle_quantity'],
                'received_date' => now()->format("Y-m-d"),
                $store->packed_column => $row['bundle_quantity'],
                $store->yard_column => $row['yard_quantity'],
                'supplier_id' => $supplier->id,
                'stock_id' => $newStock->id
            ]);

            $newStock->stockbatches()->save($stockBatch);
        }


    }


}

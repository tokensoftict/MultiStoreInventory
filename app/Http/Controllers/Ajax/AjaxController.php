<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Stockbatch;
use App\Models\Warehousestore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AjaxController extends Controller
{
    public function findstock(Request $request){

        $result = [];

        if($request->get('searchTerm') && $request->get('query')){
            return response()->json($result);
        }

        $query = ($request->get('query', null) ? $request->get('query', null) : $request->get('searchTerm', null));

        if($query === null)
        {
            $query = $request->get('q', "");
        }

        if(empty($query)) {
            return response()->json($result);
        }

        $query =  explode(' ', $query);

        $available = Stockbatch::select(
            'stock_id',
            'stock_id as id',
            'stocks.name as text',
        )->join('stocks', 'stocks.id','=','stockbatches.stock_id')->with(['stock'])->where(function($query) use (&$warehouses){
            $query->orWhere(getActiveStore()->packed_column,'>',0);
            $query->orWhere(getActiveStore()->yard_column,'>',0);
        })->whereHas('stock',function($q) use (&$query){
            $q->where('status',1);
            $q->where('type','!=','NON-SALEABLE-ITEMS');
            $q->where(function($sub) use (&$query){
                foreach ($query as $char) {
                    $sub->where('name', 'LIKE', "%{$char}%");
                }
            });
            $q->orWhere('barcode', "=", $query);
        })->groupBy('stock_id')->get();

        return $available;
    }



    public function findanystock(Request $request){

        $result = [];

        if($request->get('searchTerm') && $request->get('query')){
            return response()->json($result);
        }

        $query = ($request->get('query') ? $request->get('query') : $request->get('searchTerm'));


        if(empty($query)) {
            return response()->json($result);
        }

        $query =  explode(' ', $query);

        $available = Stockbatch::select(
            'stock_id'
        )->with(['stock'])
            ->whereHas('stock',function($q) use (&$query){
            $q->where('status',1);
            $q->where('type','!=','NON-SALEABLE-ITEMS');
            $q->where(function($sub) use (&$query){
                foreach ($query as $char) {
                    $sub->where('name', 'LIKE', "%{$char}%");
                }
            });
            $q->orWhere('barcode', "=", $query);
        })->groupBy('stock_id')->get();

        return $available;
    }





    public function findselectstock(Request $request){
        $result = [];

        if($request->get('searchTerm') && $request->get('query')){
            return response()->json($result);
        }

        $query = ($request->get('query') ? $request->get('query') : $request->get('searchTerm'));


        if(empty($query)) {
            return response()->json($result);
        }

        $query =  explode(' ', $query);

        $stocks = Stockbatch::select(
            'stock_id'
        )->with(['stock'])->where(function($query) use (&$warehouses, $request){
            if(!$request->type) {
                $query->orWhere(getActiveStore()->packed_column, '>', 0);
                $query->orWhere(getActiveStore()->yard_column, '>', 0);
            }else{
                if(!$request->store) {
                    if ($request->type == "NORMAL") {
                        $query->where(getActiveStore()->packed_column, '>', 0);
                    }
                    if ($request->type == "PACKED") {
                        $query->where(getActiveStore()->yard_column, '>', 0);
                    }
                }else{
                    $store = Warehousestore::find($request->store);
                    if ($request->type == "NORMAL") {
                        $query->where($store->packed_column, '>', 0);
                    }
                    if ($request->type == "PACKED") {
                        $query->where($store->yard_column, '>', 0);
                    }
                }
            }
        })->whereHas('stock',function($q) use (&$query){
            $q->where('status',1);
            $q->where('type','!=','NON-SALEABLE-ITEMS');
            $q->where(function($sub) use (&$query){
                foreach ($query as $char) {
                    $sub->where('name', 'LIKE', "%{$char}%");
                }
            });
            $q->orWhere('barcode', "=", $query);
        })->groupBy('stock_id')->get();

        foreach ($stocks as $stock) {
            if(isset($request->store)) {
                $result[] = [
                    'available_quantity' => $stock->stock->getCustomPackedStockQuantity($request->store),
                    'available_yard_quantity' => $stock->stock->getCustomYardStockQuantity($request->store),
                    'text' => $stock->stock->name,
                    "id" => $stock->stock->id,
                    'cost_price' => getStockActualCostPrice($stock->stock, $request->type),
                    'selling_price' => getStockActualSellingPrice($stock->stock, $request->type)
                ];
            }else{
                $result[] = [
                    'available_quantity' => $stock->stock->available_quantity,
                    'available_yard_quantity' => $stock->stock->available_yard_quantity,
                    'text' => $stock->stock->name,
                    "id" => $stock->stock->id,
                    'cost_price' => getStockActualCostPrice($stock->stock, $request->type),
                    'selling_price' => getStockActualSellingPrice($stock->stock, $request->type)
                ];
            }
        }
        return response()->json($result);

    }



    public function findpurchaseorderstock(Request $request){
        $result = [];
        /*
        if($request->get('searchTerm') && $request->get('query')){
            return response()->json($result);
        }
        */
        $query = ($request->get('query') ? $request->get('query') : $request->get('searchTerm'));


        if(empty($query)) {
            return response()->json($result);
        }

        $query =  explode(' ', $query);

        $stocks = Stock::where('status',1)
            ->where('type','!=','NON-SALEABLE-ITEMS')
            ->where(function($sub) use(&$query){
                foreach ($query as $char) {
                    $sub->where('name', 'LIKE', "%{$char}%");
                }
            })->orWhere('barcode', "=", $query)->get();

        foreach ($stocks as $stock) {
            $result[] = [
                'available_quantity' => $stock->available_quantity,
                'available_yard_quantity' => $stock->available_yard_quantity,
                'text' => $stock->name,
                "id" => $stock->id,
                'cost_price' =>$stock->cost_price,
                'selling_price' =>$stock->selling_price
            ];
        }
        return response()->json($result);

    }





    public function findimage(Request $request){
        $name = $request->get('name');

        foreach (['JPG','jpg','PNG','png','JPEG','jpeg'] as $extension)
        {
            if (is_file(public_path('product_image/' . $name. ".".$extension))) {
                $ext = pathinfo(public_path('product_image/' . $name . ".".$extension), PATHINFO_EXTENSION);
                return response()->json(['status'=>true,'link'=>asset('product_image/' . $name . '.' . $ext)]);
            }
        }

        return response()->json(['status'=>false]);
    }

}

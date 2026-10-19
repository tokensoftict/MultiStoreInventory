<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Stock;
use App\Models\Stockbatch;
use App\Models\Warehousestore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $user = auth()->user();
        $assignedCategoryIds = $user ? $user->price_categories()->pluck('price_categories.id')->toArray() : [];

        $available = Stockbatch::select(
            'stock_id',
            'stock_id as id',
            'stocks.name as text',
        )->join('stocks', 'stocks.id','=','stockbatches.stock_id')->with(['stock.stockPrices' => function($q) use ($assignedCategoryIds) {
            $q->whereIn('price_category_id', $assignedCategoryIds);
        }])
            /*
             * ->where(function($query) use (&$warehouses){
            $query->orWhere(getActiveStore()->packed_column,'>',0);
            $query->orWhere(getActiveStore()->yard_column,'>',0);
        })
             */


            ->whereHas('stock',function($q) use (&$query){
                $q->where('status',"1");
                $q->where('type','!=','NON-SALEABLE-ITEMS');
                $q->where(function($sub) use (&$query){
                    foreach ($query as $char) {
                        $sub->where('name', 'LIKE', "%{$char}%");
                    }
                });
                $q->orWhere('barcode', "=", $query);
            })->groupBy('stock_id')->get();

        $multiLevel = \App\Services\StockUnitService::isMulti();

        $data = $available->map(function($stock) use ($multiLevel) {
            // Convert model to array and append the dynamic variable
            $stockArray = $stock->toArray();
            $stockArray['show_qty'] = userCanView('invoiceandsales.showqty');
            if ($multiLevel && \App\Services\StockUnitService::hasUnits($stock->stock_id)) {
                $base = (float)$stock->stock->available_yard_quantity;
                $stockArray['stock']['multi_level'] = true;
                $stockArray['stock']['base_quantity'] = $base;
                $stockArray['stock']['units'] = \App\Services\StockUnitService::unitsForUi($stock->stock_id, $base);
                $stockArray['stock']['only_pieces'] = \App\Models\QuantityUnit::isOnlyPiecesEnabled();
                $stockArray['stock']['stock_label'] = \App\Services\StockUnitService::usableBreakdownLabel($stock->stock_id, $base);
            }
            return $stockArray;
        });

        return response()->json($data);
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

        $multiLevel = \App\Services\StockUnitService::isMulti();

        $data = $available->map(function($stock) use ($multiLevel) {
            $stockArray = $stock->toArray();
            $stockArray['show_qty'] = userCanView('invoiceandsales.showqty');
            if ($multiLevel && \App\Services\StockUnitService::hasUnits($stock->stock_id)) {
                $base = (float)$stock->stock->available_yard_quantity;
                $stockArray['stock']['multi_level'] = true;
                $stockArray['stock']['base_quantity'] = $base;
                $stockArray['stock']['units'] = \App\Services\StockUnitService::unitsForUi($stock->stock_id, $base);
                $stockArray['stock']['only_pieces'] = \App\Models\QuantityUnit::isOnlyPiecesEnabled();
                $stockArray['stock']['stock_label'] = \App\Services\StockUnitService::usableBreakdownLabel($stock->stock_id, $base);
            }
            return $stockArray;
        });

        return response()->json($data);
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

        $targetStore = null;
        if ($request->store) {
            $targetStore = Warehousestore::find($request->store);
        }
        if (!$targetStore) {
            $targetStore = getActiveStore();
        }

        $stocks = Stockbatch::select(
            'stock_id'
        )->with(['stock'])->where(function($query) use ($targetStore, $request){
            if(!$request->type) {
                $query->where(function($sub) use ($targetStore) {
                    $sub->orWhere($targetStore->packed_column, '>', 0);
                    $sub->orWhere($targetStore->yard_column, '>', 0);
                });
            }else{
                if ($request->type == "NORMAL") {
                    $query->where($targetStore->packed_column, '>', 0);
                }
                if ($request->type == "PACKED") {
                    $query->where($targetStore->yard_column, '>', 0);
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

        $isMulti = $targetStore ? \App\Services\StockUnitService::isMulti($targetStore->id) : \App\Services\StockUnitService::isMulti();

        foreach ($stocks as $stock) {
            $packedQty = isset($request->store)
                ? $stock->stock->getCustomPackedStockQuantity($request->store)
                : $stock->stock->available_quantity;
            $yardQty = isset($request->store)
                ? $stock->stock->getCustomYardStockQuantity($request->store)
                : $stock->stock->available_yard_quantity;

            $item = [
                'available_quantity' => $packedQty,
                'available_yard_quantity' => $yardQty,
                'text' => $stock->stock->name,
                "id" => $stock->stock->id,
                'cost_price' => getStockActualCostPrice($stock->stock, 'NORMAL'),
                'selling_price' => getStockActualSellingPrice($stock->stock, 'NORMAL'),
                'yard_cost_price' => getStockActualCostPrice($stock->stock, 'PACKED'),
                'yard_selling_price' => getStockActualSellingPrice($stock->stock, 'PACKED'),
            ];

            if ($isMulti && \App\Services\StockUnitService::hasUnits($stock->stock_id)) {
                $base = (float)$yardQty;
                $item['multi_level'] = true;
                $item['base_quantity'] = $base;
                $item['units'] = \App\Services\StockUnitService::unitsForUi($stock->stock_id, $base);
                $item['only_pieces'] = \App\Models\QuantityUnit::isOnlyPiecesEnabled();
                $item['stock_label'] = \App\Services\StockUnitService::usableBreakdownLabel($stock->stock_id, $base);
            }

            $result[] = $item;
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

        $multiLevel = \App\Services\StockUnitService::isMulti();

        foreach ($stocks as $stock) {
            $row = [
                'available_quantity' => $stock->available_quantity,
                'available_yard_quantity' => $stock->available_yard_quantity,
                'text' => $stock->name,
                "id" => $stock->id,
                'cost_price' =>$stock->cost_price,
                'selling_price' =>$stock->selling_price
            ];
            if ($multiLevel && \App\Services\StockUnitService::hasUnits($stock->id)) {
                $row['units'] = \App\Services\StockUnitService::unitsForUi($stock->id);
                $row['only_pieces'] = \App\Models\QuantityUnit::isOnlyPiecesEnabled();
            }
            $result[] = $row;
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


    public function processScaninvoice(Request $request){
        $invoiceCode = $request->get('invoice_code');

        $invoice =  Invoice::where(function($sub) use($invoiceCode){
            $sub->orWhere('id', $invoiceCode)->orWhere('invoice_number', $invoiceCode);
        })->first();

        if(!$invoice){
            return response()->json(['status'=>false, "message" => "Invoice not found, Please try again."]);
        }

        if(!is_null($invoice->scan_user_id)){
            return response()->json(['status'=>false, "message" => "Invoice has already been scanned by ".$invoice->scan_by->name." this might be a duplicate receipt"]);
        }

        $invoice->scan_user_id = \auth()->id();
        $invoice->scan_time = Carbon::now();
        $invoice->scan_date = now()->format('Y-m-d');
        $invoice->update();

        return response()->json(['status'=>true]);
    }

    public function update_price_category_price(Request $request)
    {
        if (!userCanView('stock.edit')) {
            return response()->json(['status' => false, 'message' => 'Unauthorized action. You do not have permission to edit stock prices.']);
        }

        $stockId = $request->get('stock_id');
        $categoryId = $request->get('price_category_id');
        $price = $request->get('price');

        if (!$stockId || !$categoryId) {
            return response()->json(['status' => false, 'message' => 'Missing required data']);
        }

        \App\Models\StockPrice::updateOrCreate(
            ['stock_id' => $stockId, 'price_category_id' => $categoryId],
            ['price' => $price]
        );

        return response()->json(['status' => true, 'message' => 'Price updated successfully']);
    }

}

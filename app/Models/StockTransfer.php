<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class StockTransfer
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $from
 * @property int $to
 * @property string $type
 * @property string $status
 * @property Carbon $transfer_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $total_price
 * @property Warehousestore $warehousestore
 * @property User|null $user
 * @property Collection|StockTransferItem[] $stock_transfer_items
 *
 * @package App\Models
 */
class StockTransfer extends Model
{


    protected $table = 'stock_transfers';

    protected $casts = [
        'user_id' => 'int',
        'from' => 'int',
        'to' => 'int',
        'total_price' => 'int',
    ];

    protected $dates = [
        'transfer_date'
    ];

    protected $fillable = [
        'user_id',
        'from',
        'to',
        'type',
        'status',
        'total_price',
        'transfer_date'
    ];

    public function store_to()
    {
        return $this->belongsTo(Warehousestore::class, 'to');
    }

    public function store_from()
    {
        return $this->belongsTo(Warehousestore::class, 'from');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stock_transfer_items()
    {
        return $this->hasMany(StockTransferItem::class);
    }


    public function complete()
    {

        if($this->status == "COMPLETE") return redirect()->route('stocktransfer.index')->with('success','stock transfer was completed successfully');

        $from_store = getActualStore($this->type, $this->from);

        $to_store = getActualStore($this->type, $this->to);

        $items = $this->stock_transfer_items()->get();
        foreach ($items as $item)
        {
            $batches =  $item->stock->getSaleableBatches($from_store,$item->quantity);

            if($batches == false)
                return redirect()->route('stocktransfer.transfer_report')->with('error','Not Enough quantity to transfer '.$item->stock->name);
        }

        foreach ($items as $item)
        {
            $batches =  $item->stock->getSaleableBatches($from_store,$item->quantity);

            $item->stock->removeSaleableBatches($batches);

            foreach ($batches as $key => $batch){
                StockLogOperation::createOperationLog([
                    'stock_id' => $item->stock->id,
                    'user_id' =>  auth()->id(),
                    'selling_price' => $item->selling_price,
                    'cost_price' => $item->cost_price,
                    'operation_type' => StockTransferItem::class,
                    'operation_id' => $item->id,
                    'quantity' => $batch['qty'],
                    'store' => $from_store,
                    'stockbatch_id' => $key,
                    'log_date' =>  $this->transfer_date
                ]);
            }

            $item->stock->addSaleableBatches($batches, $to_store);

            foreach ($batches as $key => $batch){
                StockLogOperation::createOperationLog([
                    'stock_id' => $item->stock->id,
                    'user_id' =>  auth()->id(),
                    'selling_price' => $item->selling_price,
                    'cost_price' => $item->cost_price,
                    'operation_type' => StockTransferItem::class,
                    'operation_id' => $item->id,
                    'quantity' => $batch['qty'],
                    'store' => $to_store,
                    'stockbatch_id' => $key,
                    'log_date' =>  $this->transfer_date
                ]);
            }

        }

        $this->status = "COMPLETE";
        $this->update();

        return redirect()->route('stocktransfer.index')->with('success','stock transfer was completed successfully');

    }

    public static function createStockTransfer($request){
        $qty = $request->get("qty");
        $stocks = $request->get("stock_id");

        $transfer_item = [];

        $total = 0;

        $selected_stocks = Stock::whereIn('id', $stocks)->get();

        foreach ($selected_stocks as $key =>$selected_stock)
        {
            $total+=getStockActualCostPrice($selected_stock, $request->type) * $qty[$key];
            $transfer_item[] = new StockTransferItem(
                [
                    'stock_id' => $selected_stock->id,
                    'user_id' => auth()->id(),
                    'product_type' => $request->type,
                    'from' => $request->from,
                    'to' => $request->to,
                    'selling_price' => getStockActualSellingPrice($selected_stock, $request->type),
                    'cost_price' => getStockActualCostPrice($selected_stock, $request->type),
                    'quantity' => $qty[$key],
                    'transfer_date' => $request->transfer_date,
                ]
            );
        }

        $transfer = StockTransfer::create([
            'status' => "DRAFT",
            'user_id' => auth()->id(),
            'total_price' => $total,
            'from' =>  $request->from,
            'type' => $request->type,
            'to' => $request->to,
            'transfer_date' => $request->transfer_date,
        ]);
        $transfer->stock_transfer_items() ->saveMany($transfer_item);


        if($request->status == "COMPLETE"){
            return $transfer->complete();
        }

        return redirect()->route('stocktransfer.index')->with('success','stock transfer has ben draft successfully');
    }

    public static function updateStockTransfer($request, $id){

        $transfer = StockTransfer::findorfail($id);

        $qty = $request->get("qty");
        $stocks = $request->get("stock_id");

        $transfer_item = [];

        $total = 0;

        $selected_stocks = Stock::whereIn('id', $stocks)->get();

        foreach ($selected_stocks as $key =>$selected_stock)
        {
            $total+=getStockActualCostPrice($selected_stock, $request->type) * $qty[$key];
            $transfer_item[] = new StockTransferItem(
                [
                    'stock_id' => $selected_stock->id,
                    'user_id' => auth()->id(),
                    'product_type' => $request->type,
                    'from' => $request->from,
                    'to' => $request->to,
                    'selling_price' => getStockActualSellingPrice($selected_stock, $request->type),
                    'cost_price' => getStockActualSellingPrice($selected_stock, $request->type),
                    'quantity' => $qty[$key],
                    'transfer_date' => $request->transfer_date,
                ]
            );
        }

        $transfer->update([
            'status' => "DRAFT",
            'user_id' => auth()->id(),
            'total_price' => $total,
            'transfer_date' => $request->transfer_date,
        ]);

        $transfer->stock_transfer_items()->delete();

        $transfer->stock_transfer_items() ->saveMany($transfer_item);


        return redirect()->route('stocktransfer.transfer_report')->with('success','stock transfer has ben updated successfully');
    }

/*
    public static function __createStockTransfer($request)
    {
        $from_store = getActualStore($request->product_type, $request->from);

        $to_store = getActualStore($request->product_type, $request->to);

        $stock = Stock::findorfail($request->stock_id);

        $batches =  $stock->getSaleableBatches($from_store,$request->qty);

        if($request->from == $request->to)
            return redirect()->route('stocktransfer.add_transfer')->with('error','Sorry, You can transfer stock to the store');

        if($batches == false)
            return redirect()->route('stocktransfer.add_transfer')->with('error','Not enough quantity to transfer, please check available quantity');

        $transfer = StockTransferBackup::create(
            [
                'stock_id' => $stock->id,
                'user_id' => auth()->id(),
                'product_type' => $request->product_type,
                'from' => $request->from,
                'to' => $request->to,
                'selling_price' => getStockActualSellingPrice($stock,$request->product_type),
                'cost_price' => getStockActualCostPrice($stock, $request->product_type),
                'quantity' => $request->qty,
                'transfer_date' => $request->date_created,
            ]
        );

        $stock->removeSaleableBatches($batches);

        foreach ($batches as $key => $batch){
            StockLogOperation::createOperationLog([
                'stock_id' => $stock->id,
                'user_id' =>  auth()->id(),
                'selling_price' => $transfer->selling_price,
                'cost_price' => $transfer->cost_price,
                'operation_type' => "App\\Models\\StockTransfer",
                'operation_id' => $transfer->id,
                'quantity' => $batch['qty'],
                'store' => $from_store,
                'stockbatch_id' => $key,
                'log_date' =>  $request->date_created
            ]);
        }

        $stock->addSaleableBatches($batches, $to_store);

        foreach ($batches as $key => $batch){
            StockLogOperation::createOperationLog([
                'stock_id' => $stock->id,
                'user_id' =>  auth()->id(),
                'selling_price' => $transfer->selling_price,
                'cost_price' => $transfer->cost_price,
                'operation_type' => "App\\Models\\StockTransfer",
                'operation_id' => $transfer->id,
                'quantity' => $batch['qty'],
                'store' => $to_store,
                'stockbatch_id' => $key,
                'log_date' =>  $request->date_created
            ]);
        }

        return redirect()->route('stocktransfer.add_transfer')->with('success','Stock has been transferred successfully!');

    }

*/

}

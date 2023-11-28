<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockTransfer
 * 
 * @property int $id
 * @property int $stock_id
 * @property int|null $user_id
 * @property string|null $product_type
 * @property int $from
 * @property int $to
 * @property float $selling_price
 * @property float $cost_price
 * @property int $quantity
 * @property Carbon $transfer_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Warehousestore $warehousestore
 * @property Stock $stock
 * @property User|null $user
 *
 * @package App\Models
 */
class StockTransferBackup extends Model
{
	protected $table = 'stock_transfers';

	protected $casts = [
		'stock_id' => 'int',
		'user_id' => 'int',
		'from' => 'int',
		'to' => 'int',
		'selling_price' => 'float',
		'cost_price' => 'float',
		'quantity' => 'int'
	];

	protected $dates = [
		'transfer_date'
	];

	protected $fillable = [
		'stock_id',
		'user_id',
		'product_type',
		'from',
		'to',
		'selling_price',
		'cost_price',
		'quantity',
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

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}


	public function operation()
    {
	    return $this->morphMany(StockLogOperation::class,'operation');
    }

	public static function createStockTransfer($request)
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
}

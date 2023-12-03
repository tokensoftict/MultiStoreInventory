<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockLogItem
 * 
 * @property int $id
 * @property int $stock_id
 * @property int|null $user_id
 * @property int|null $warehousestore_id
 * @property string|null $product_type
 * @property float $selling_price
 * @property float $cost_price
 * @property int $quantity
 * @property string|null $usage_type
 * @property string|null $department
 * @property Carbon $log_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $stock_log_usages_type_id
 * 
 * @property Stock $stock
 * @property StockLogUsagesType|null $stock_log_usages_type
 * @property User|null $user
 * @property Warehousestore|null $warehousestore
 *
 * @package App\Models
 */
class StockLogItem extends Model
{
	protected $table = 'stock_log_items';

	protected $casts = [
		'stock_id' => 'int',
		'user_id' => 'int',
		'warehousestore_id' => 'int',
		'selling_price' => 'float',
		'cost_price' => 'float',
		'quantity' => 'int',
		'stock_log_usages_type_id' => 'int'
	];

	protected $dates = [
		'log_date'
	];

	protected $fillable = [
		'stock_id',
		'user_id',
		'warehousestore_id',
		'product_type',
		'selling_price',
		'cost_price',
		'quantity',
		'usage_type',
		'department',
		'log_date',
		'stock_log_usages_type_id'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function stock_log_usages_type()
	{
		return $this->belongsTo(StockLogUsagesType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function warehousestore()
	{
		return $this->belongsTo(Warehousestore::class);
	}


    public function operation()
    {
        return $this->morphMany(StockLogOperation::class,'operation');
    }

    public static function createStockLog($request)
    {

        $store_column = getActualStore($request->product_type, $request->store);

        $stock = Stock::findorfail($request->stock_id);

        $batches = $stock->getSaleableBatches($store_column, $request->qty);

        if ($batches == false) {
            return redirect()->route('stocklog.add_log')->with('error', 'Not enough quantity to log stock, please check available quantity');
        }

        $usage = StockLogUsagesType::findorfail($request->usage_type);

        $logItem = StockLogItem::create(
            [
                'stock_id' => $stock->id,
                'quantity' => $request->qty,
                'usage_type' => $usage->name,
                'stock_log_usages_type_id' => $request->usage_type,
                'department' => $request->department,
                'log_date' => $request->date_created,
                'warehousestore_id' => $request->store,
                'cost_price' => getStockActualCostPrice($stock, $request->product_type),
                'product_type' => $request->product_type,
                'selling_price' => getStockActualSellingPrice($stock, $request->product_type),
                'user_id' => auth()->id(),
            ]
        );


        foreach ($batches as $key => $batch) {
            StockLogOperation::createOperationLog([
                'stock_id' => $stock->id,
                'user_id' => auth()->id(),
                'selling_price' => $logItem->selling_price,
                'cost_price' => $logItem->cost_price,
                'operation_type' => "App\\Models\\StockLogItem",
                'operation_id' => $logItem->id,
                'quantity' => $batch['qty'],
                'store' => $store_column,
                'stockbatch_id' => $key,
                'log_date' => $request->date_created
            ]);
        }

        $stock->removeSaleableBatches($batches);

        return redirect()->route('stocklog.add_log')->with('success', 'Stock Log has been created successfully!');

    }

    public static function updateStockLog($id, $request)
    {

        $store_column = getActualStore($request->product_type, $request->store);

        $log = StockLogItem::with(['user', 'stock', 'operation', 'warehousestore'])->find($id);

        foreach ($log->operation as $operation) {
            $operation->returnStockBack();
        }

        $batches = $log->stock->getSaleableBatches($store_column, $request->qty);

        if ($batches == false) {
            foreach ($log->operation as $operation) {
                $operation->minusStockBack();
            }
            return redirect()->route('stocklog.add_log')->with('error', 'Not enough quantity to log stock, please check available quantity');
        }

        $usage = StockLogUsagesType::findorfail($request->usage_type);

        $log->update(
            [
                'quantity' => $request->qty,
                'usage_type' => $usage->name,
                'stock_log_usages_type_id' => $request->usage_type,
                'department' => $request->department,
                'log_date' => $request->date_created,
                'warehousestore_id' => $request->store,
                'cost_price' => getStockActualCostPrice($log->stock, $request->product_type),
                'product_type' => $request->product_type,
                'selling_price' => getStockActualSellingPrice($log->stock, $request->product_type),
                'user_id' => auth()->id(),
            ]
        );


        $log->operation()->delete();

        foreach ($batches as $key => $batch) {
            StockLogOperation::createOperationLog([
                'stock_id' => $log->stock->id,
                'user_id' => auth()->id(),
                'selling_price' => $log->selling_price,
                'cost_price' => $log->cost_price,
                'operation_type' => "App\\Models\\StockLogItem",
                'operation_id' => $log->id,
                'quantity' => $batch['qty'],
                'store' => $store_column,
                'stockbatch_id' => $key,
                'log_date' => $request->date_created
            ]);
        }

        $log->stock->removeSaleableBatches($batches);

        return redirect()->route('stocklog.add_log')->with('success', 'Stock Log has been updated successfully!');

    }
}

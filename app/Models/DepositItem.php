<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DepositItem
 * 
 * @property int $id
 * @property int $deposit_id
 * @property int|null $stock_id
 * @property int|null $warehousestore_id
 * @property string $department
 * @property int $quantity
 * @property int|null $customer_id
 * @property string $status
 * @property int $added_by
 * @property Carbon $deposit_date
 * @property string $store
 * @property Carbon $deposit_time
 * @property float $cost_price
 * @property float $selling_price
 * @property float $profit
 * @property float $total_cost_price
 * @property float $total_selling_price
 * @property float $total_profit
 * @property string|null $discount_type
 * @property float|null $discount_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Customer|null $customer
 * @property Deposit $deposit
 * @property Stock|null $stock
 * @property Warehousestore|null $warehousestore
 *
 * @package App\Models
 */
class DepositItem extends Model
{
	protected $table = 'deposit_items';

	protected $casts = [
		'deposit_id' => 'int',
		'stock_id' => 'int',
		'warehousestore_id' => 'int',
		'quantity' => 'int',
		'customer_id' => 'int',
		'added_by' => 'int',
		'cost_price' => 'float',
		'selling_price' => 'float',
		'profit' => 'float',
		'total_cost_price' => 'float',
		'total_selling_price' => 'float',
		'total_profit' => 'float',
		'discount_amount' => 'float'
	];

	protected $dates = [
		'deposit_date',
		'deposit_time'
	];

	protected $fillable = [
		'deposit_id',
		'stock_id',
		'warehousestore_id',
		'department',
		'quantity',
		'customer_id',
		'status',
		'added_by',
		'deposit_date',
		'store',
		'deposit_time',
		'cost_price',
		'selling_price',
		'profit',
		'total_cost_price',
		'total_selling_price',
		'total_profit',
		'discount_type',
		'discount_amount'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function deposit()
	{
		return $this->belongsTo(Deposit::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function warehousestore()
	{
		return $this->belongsTo(Warehousestore::class);
	}
}

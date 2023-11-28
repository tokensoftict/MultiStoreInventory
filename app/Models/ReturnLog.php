<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class ReturnLog
 * 
 * @property int $id
 * @property string|null $store_after
 * @property int $stock_id
 * @property int|null $user_id
 * @property int|null $customer_id
 * @property int|null $warehousestore_id
 * @property string|null $invoice_number
 * @property string|null $invoice_paper_number
 * @property Carbon $date_added
 * @property int $quantity_before
 * @property int $quantity_after
 * @property float $selling_price
 * @property int $dif
 * @property string|null $store_before
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Customer|null $customer
 * @property Stock $stock
 * @property User|null $user
 * @property Warehousestore|null $warehousestore
 *
 * @package App\Models
 */
class ReturnLog extends Model
{
    use LogsActivity;

	protected $table = 'return_logs';

	protected $casts = [
		'stock_id' => 'int',
		'user_id' => 'int',
		'customer_id' => 'int',
		'warehousestore_id' => 'int',
		'quantity_before' => 'int',
		'quantity_after' => 'int',
		'selling_price' => 'float',
		'dif' => 'int'
	];

	protected $dates = [
		'date_added'
	];

	protected $fillable = [
		'store_after',
		'stock_id',
		'user_id',
		'customer_id',
		'warehousestore_id',
		'invoice_number',
		'invoice_paper_number',
		'date_added',
		'quantity_before',
		'quantity_after',
		'selling_price',
		'dif',
		'store_before'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function warehousestore()
	{
		return $this->belongsTo(Warehousestore::class);
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockTakingItem
 * 
 * @property int $id
 * @property string $name
 * @property int $stock_id
 * @property int|null $available_quantity
 * @property int|null $available_yard_quantity
 * @property int|null $counted_available_quantity
 * @property int|null $counted_yard_quantity
 * @property int $available_quantity_diff
 * @property int $available_yard_quantity_diff
 * @property int $stock_taking_id
 * @property int $warehousestore_id
 * @property int|null $user_id
 * @property string $status
 * @property Carbon $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock $stock
 * @property StockTaking $stock_taking
 * @property User|null $user
 * @property Warehousestore $warehousestore
 *
 * @package App\Models
 */
class StockTakingItem extends Model
{
	protected $table = 'stock_taking_items';

	protected $casts = [
		'stock_id' => 'int',
		'available_quantity' => 'int',
		'available_yard_quantity' => 'int',
		'counted_available_quantity' => 'int',
		'counted_yard_quantity' => 'int',
		'available_quantity_diff' => 'int',
		'available_yard_quantity_diff' => 'int',
		'stock_taking_id' => 'int',
		'warehousestore_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'name',
		'stock_id',
		'available_quantity',
		'available_yard_quantity',
		'counted_available_quantity',
		'counted_yard_quantity',
		'available_quantity_diff',
		'available_yard_quantity_diff',
		'stock_taking_id',
		'warehousestore_id',
		'user_id',
		'status',
		'date'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function stock_taking()
	{
		return $this->belongsTo(StockTaking::class);
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

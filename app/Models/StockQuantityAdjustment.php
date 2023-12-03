<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockQuantityAdjustment
 * 
 * @property int $id
 * @property int $stock_id
 * @property int|null $user_id
 * @property Carbon $date_adjusted
 * @property int $from
 * @property int $to
 * @property string $type
 * @property int $warehousestore_id
 * @property string|null $changed_column
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock $stock
 * @property User|null $user
 * @property Warehousestore $warehousestore
 *
 * @package App\Models
 */
class StockQuantityAdjustment extends Model
{
	protected $table = 'stock_quantity_adjustments';

	protected $casts = [
		'stock_id' => 'int',
		'user_id' => 'int',
		'from' => 'int',
		'to' => 'int',
		'warehousestore_id' => 'int'
	];

	protected $dates = [
		'date_adjusted'
	];

	protected $fillable = [
		'stock_id',
		'user_id',
		'date_adjusted',
		'from',
		'to',
		'type',
		'warehousestore_id',
		'changed_column'
	];

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

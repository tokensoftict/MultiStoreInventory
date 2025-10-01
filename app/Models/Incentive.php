<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Incentive
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $warehousestore_id
 * @property float $amount
 * @property Carbon|null $payment_time
 * @property Carbon|null $payment_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Warehousestore|null $warehousestore
 *
 * @package App\Models
 */
class Incentive extends Model
{
	protected $table = 'incentives';

	protected $casts = [
		'user_id' => 'int',
		'warehousestore_id' => 'int',
		'amount' => 'float'
	];

	protected $dates = [
		'payment_time',
		'payment_date'
	];

	protected $fillable = [
		'user_id',
		'warehousestore_id',
		'amount',
		'payment_time',
		'payment_date'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function warehousestore()
	{
		return $this->belongsTo(Warehousestore::class);
	}
}

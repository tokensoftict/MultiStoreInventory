<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Userstoremapper
 * 
 * @property int $id
 * @property int $warehousestore_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 * @property Warehousestore $warehousestore
 *
 * @package App\Models
 */
class Userstoremapper extends Model
{
	protected $table = 'userstoremappers';

	protected $casts = [
		'warehousestore_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'warehousestore_id',
		'user_id'
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

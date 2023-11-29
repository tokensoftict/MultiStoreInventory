<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Room
 * 
 * @property int $id
 * @property int $room_type_id
 * @property int $status_id
 * @property string $number
 * @property int $capacity
 * @property float $price
 * @property string|null $view
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property RoomType $room_type
 * @property Status $status
 *
 * @package App\Models
 */
class Room extends Model
{
	use SoftDeletes;


	protected $table = 'rooms';

	protected $casts = [
		'room_type_id' => 'int',
		'status_id' => 'int',
		'capacity' => 'int',
		'price' => 'float'
	];

	protected $fillable = [
		'room_type_id',
		'status_id',
		'name',
		'capacity',
		'price',
		'view'
	];

    public static $fields = [
        'room_type_id',
        'name',
        'capacity',
        'price',
        'view',
        'status_id',
    ];


    public static $validation = [
        'room_type_id'=>"required",
        'name'=>"required",
        'capacity'=>"required",
        'price'=>"required",
    ];


    public function room_type()
	{
		return $this->belongsTo(RoomType::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}
}

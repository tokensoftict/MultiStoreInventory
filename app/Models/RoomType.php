<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class RoomType
 * 
 * @property int $id
 * @property string $name
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class RoomType extends Model
{
	protected $table = 'room_types';

	protected $casts = [
		'status' => 'bool'
	];

    protected $fillable = [
        'name',
        'status'
    ];

    public static $fields = [
        'name',
        'status'
    ];

    public static $validate = [
        'name'=>'required',
    ];
}

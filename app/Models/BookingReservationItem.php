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
 * Class BookingReservationItem
 * 
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property int $status_id
 * @property int $noOfDays
 * @property int $room_id
 * @property int $booking_reservation_id
 * @property float $total
 * @property float $price
 * @property Carbon $booking_date
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property BookingReservation $booking_reservation
 * @property Customer $customer
 * @property Room $room
 * @property Status $status
 * @property User $user
 *
 * @package App\Models
 */
class BookingReservationItem extends Model
{
	use SoftDeletes;



	protected $table = 'booking_reservation_items';

	protected $casts = [
		'customer_id' => 'int',
		'user_id' => 'int',
		'status_id' => 'int',
        'noOfDays' => 'int',
		'room_id' => 'int',
		'booking_reservation_id' => 'int',
		'total' => 'float',
        'price' => 'float'
	];

	protected $dates = [
		'booking_date',
		'start_date',
		'end_date'
	];

	protected $fillable = [
		'customer_id',
		'user_id',
		'status_id',
		'room_id',
        'noOfDays',
		'booking_reservation_id',
		'total',
        'price',
		'booking_date',
		'start_date',
		'end_date'
	];

	public function booking_reservation()
	{
		return $this->belongsTo(BookingReservation::class);
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function room()
	{
		return $this->belongsTo(Room::class);
	}

	public function status()
	{
		return $this->belongsTo(Status::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

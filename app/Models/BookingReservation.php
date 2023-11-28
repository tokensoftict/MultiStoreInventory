<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class BookingReservation
 *
 * @property int $id
 * @property int $customer_id
 * @property int $user_id
 * @property int $status_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string $customer_identity
 * @property Carbon $booking_date
 * @property float $total
 * @property float $total_paid
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Customer $customer
 * @property Status $status
 * @property User $user
 * @property Collection|BookingReservationItem[] $booking_reservation_items
 *
 * @package App\Models
 */
class BookingReservation extends Model
{
    use SoftDeletes;

    use LogsActivity;

    protected $table = 'booking_reservations';

    protected $casts = [
        'customer_id' => 'int',
        'user_id' => 'int',
        'status_id' => 'int',
        'total' => 'float',
        'total_paid' => 'float'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'booking_date'
    ];

    protected $fillable = [
        'customer_id',
        'reservation_number',
        'user_id',
        'status_id',
        'start_date',
        'end_date',
        'customer_identity',
        'booking_date',
        'total',
        'total_paid'
    ];

    public static $validation = [
        'room_id'=>'required',
        'start_date'=>'required',
        'end_date'=>'required',
        'customer_id'=>'required',
    ];

    protected $appends = ['no_of_days','no_of_rooms','invoice_number','invoice_paper_number'];


    public function getInvoiceNumberAttribute(){
        return $this->attributes['reservation_number'];
    }

    public function getInvoicePaperNumberAttribute(){
        return $this->attributes['reservation_number'];
    }

    public function getNoOfDaysAttribute(){
        $to = Carbon::createFromDate($this->start_date);
        $from = Carbon::createFromDate($this->end_date);

        return $to->diffInDays($from);
    }

    public function getNoOfRoomsAttribute(){
        return $this->booking_reservation_items()->count();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking_reservation_items()
    {
        return $this->hasMany(BookingReservationItem::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class,'invoice');
    }

    public function paymentMethodTable()
    {
        return $this->morphMany(PaymentMethodTable::class,'invoice');
    }


    public function markAsPaid(){

        $total_paid = $this->paymentMethodTable()->where('invoice_type',"App\Models\BookingReservation")->where("invoice_id",$this->id)->sum('amount');

        $this->total_paid = $total_paid;

        if($total_paid == $this->total || $total_paid > $this->total_paid)
        {
          $st_id =  Status::whereName('Paid')->first()->id;
          $this->status_id = $st_id;
        }
        else
        {
            $st_id =  Status::whereName('Partial Payment')->first()->id;
            $this->status_id = $st_id;
        }

        $this->update();
    }

}

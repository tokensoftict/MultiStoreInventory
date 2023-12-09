<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Customer
 * 
 * @property int $id
 * @property int $credit_limit
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $email
 * @property string|null $address
 * @property string|null $phone_number
 * @property string|null $nok_phone_number
 * @property string|null $nok_email
 * @property string|null $nok_lastname
 * @property string|null $nok_firstname
 * @property string|null $nationality
 * @property string|null $passport_expire_date
 * @property string|null $passport_no
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|BookingReservationItem[] $booking_reservation_items
 * @property Collection|BookingReservation[] $booking_reservations
 * @property Collection|CreditPaymentLog[] $credit_payment_logs
 * @property Collection|InvoiceItemBatch[] $invoice_item_batches
 * @property Collection|InvoiceItem[] $invoice_items
 * @property Collection|Invoice[] $invoices
 * @property Collection|PaymentMethodTable[] $payment_method_tables
 * @property Collection|Payment[] $payments
 * @property Collection|ReturnLog[] $return_logs
 *
 * @package App\Models
 */
class Customer extends Model
{


	protected $table = 'customers';

	protected $fillable = [
		'firstname',
		'lastname',
		'email',
		'address',
		'phone_number',
		'nok_phone_number',
		'nok_email',
		'nok_lastname',
		'nok_firstname',
		'nationality',
		'passport_expire_date',
		'passport_no',

        'occupation',
        'purpose_of_visit',
        'vehicle_reg_number',
        'arriving_from',
        'city',
        'state',
        'credit_limit'
	];

	public static $fields = [
        'firstname',
        'lastname',
        'email',
        'address',
        'phone_number',
        'nok_phone_number',
        'nok_email',
        'nok_lastname',
        'nok_firstname',
        'nationality',
        'passport_expire_date',
        'passport_no',

        'occupation',
        'purpose_of_visit',
        'vehicle_reg_number',
        'arriving_from',
        'city',
        'state',
        'credit_limit'
    ];

    protected $appends = ['credit_balance'];

    public static $validate = [
        'firstname'=>'required',
        'lastname'=>'required',
        'phone_number' => 'required'
    ];

    public function getCreditBalanceAttribute()
    {
        return $this->credit_payment_logs()->sum('amount');
    }


	public function booking_reservation_items()
	{
		return $this->hasMany(BookingReservationItem::class);
	}

	public function booking_reservations()
	{
		return $this->hasMany(BookingReservation::class);
	}

	public function credit_payment_logs()
	{
		return $this->hasMany(CreditPaymentLog::class);
	}

	public function invoice_item_batches()
	{
		return $this->hasMany(InvoiceItemBatch::class);
	}

	public function invoice_items()
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function payment_method_tables()
	{
		return $this->hasMany(PaymentMethodTable::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}

	public function return_logs()
	{
		return $this->hasMany(ReturnLog::class);
	}
}

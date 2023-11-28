<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class CreditPaymentLog
 * 
 * @property int $id
 * @property int $payment_id
 * @property int|null $user_id
 * @property int $payment_method_id
 * @property int|null $customer_id
 * @property int|null $invoice_number
 * @property int|null $invoice_id
 * @property float $amount
 * @property Carbon $payment_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Customer|null $customer
 * @property Invoice|null $invoice
 * @property Payment $payment
 * @property PaymentMethodTable $payment_method_table
 * @property User|null $user
 *
 * @package App\Models
 */
class CreditPaymentLog extends Model
{
    use LogsActivity;

	protected $table = 'credit_payment_logs';

	protected $casts = [
		'payment_id' => 'int',
		'user_id' => 'int',
		'payment_method_id' => 'int',
		'customer_id' => 'int',
		'invoice_number' => 'int',
		'invoice_id' => 'int',
		'amount' => 'float'
	];

	protected $dates = [
		'payment_date'
	];

	protected $fillable = [
		'payment_id',
		'user_id',
		'payment_method_id',
		'customer_id',
		'invoice_number',
		'invoice_id',
		'amount',
		'payment_date'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function payment()
	{
		return $this->belongsTo(Payment::class);
	}

	public function payment_method_table()
	{
		return $this->belongsTo(PaymentMethodTable::class, 'payment_method_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

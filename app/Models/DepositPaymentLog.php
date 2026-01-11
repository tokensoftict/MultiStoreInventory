<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DepositPaymentLog
 * 
 * @property int $id
 * @property int|null $payment_id
 * @property int|null $payment_method_id
 * @property int|null $user_id
 * @property int|null $customer_id
 * @property string|null $deposit_number
 * @property int|null $deposit_id
 * @property float $amount
 * @property Carbon $payment_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Customer|null $customer
 * @property Deposit|null $deposit
 * @property Payment|null $payment
 * @property PaymentMethodTable|null $payment_method_table
 * @property User|null $user
 *
 * @package App\Models
 */
class DepositPaymentLog extends Model
{
	protected $table = 'deposit_payment_logs';

	protected $casts = [
		'payment_id' => 'int',
		'payment_method_id' => 'int',
		'user_id' => 'int',
		'customer_id' => 'int',
		'deposit_id' => 'int',
		'amount' => 'float'
	];

	protected $dates = [
		'payment_date'
	];

	protected $fillable = [
		'payment_id',
		'payment_method_id',
		'user_id',
		'customer_id',
		'deposit_number',
		'deposit_id',
		'amount',
		'payment_date'
	];

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function deposit()
	{
		return $this->belongsTo(Deposit::class);
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

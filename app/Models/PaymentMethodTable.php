<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class PaymentMethodTable
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $customer_id
 * @property int|null $payment_id
 * @property int|null $payment_method_id
 * @property string $invoice_type
 * @property int $invoice_id
 * @property Carbon $payment_date
 * @property float $amount
 * @property string|null $payment_info
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Customer|null $customer
 * @property Payment|null $payment
 * @property PaymentMethod|null $payment_method
 * @property User|null $user
 *
 * @package App\Models
 */
class PaymentMethodTable extends Model
{
    use LogsActivity;

	protected $table = 'payment_method_table';

	protected $casts = [
		'user_id' => 'int',
		'customer_id' => 'int',
		'payment_id' => 'int',
        'warehousestore_id' => 'int',
		'payment_method_id' => 'int',
		'invoice_id' => 'int',
		'amount' => 'float'
	];

	protected $dates = [
		'payment_date'
	];

	protected $fillable = [
		'user_id',
		'customer_id',
		'payment_id',
        'department',
		'payment_method_id',
        'warehousestore_id',
		'invoice_type',
		'invoice_id',
		'payment_date',
		'amount',
		'payment_info'
	];

    public function warehousestore()
    {
        return $this->belongsTo(Warehousestore::class);
    }

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function payment()
	{
		return $this->belongsTo(Payment::class);
	}

	public function payment_method()
	{
		return $this->belongsTo(PaymentMethod::class);
	}

    public function invoice(){

        return $this->morphTo();
    }

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

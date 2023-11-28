<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class SupplierCreditPaymentHistory
 * 
 * @property int $id
 * @property int|null $user_id
 * @property int|null $supplier_id
 * @property int|null $purchase_order_id
 * @property int|null $payment_method_id
 * @property string|null $payment_info
 * @property float $amount
 * @property Carbon $payment_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PaymentMethodTable|null $payment_method_table
 * @property PurchaseOrder|null $purchase_order
 * @property Supplier|null $supplier
 * @property User|null $user
 *
 * @package App\Models
 */
class SupplierCreditPaymentHistory extends Model
{
    use LogsActivity;

	protected $table = 'supplier_credit_payment_history';

	protected $casts = [
		'user_id' => 'int',
		'supplier_id' => 'int',
		'purchase_order_id' => 'int',
		'payment_method_id' => 'int',
		'amount' => 'float'
	];

	protected $dates = [
		'payment_date'
	];

	protected $fillable = [
		'user_id',
		'supplier_id',
		'purchase_order_id',
		'payment_method_id',
		'payment_info',
		'amount',
		'payment_date'
	];

	public function payment_method_table()
	{
		return $this->belongsTo(PaymentMethodTable::class, 'payment_method_id');
	}

	public function purchase_order()
	{
		return $this->belongsTo(PurchaseOrder::class);
	}

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

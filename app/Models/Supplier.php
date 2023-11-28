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
 * Class Supplier
 * 
 * @property int $id
 * @property string|null $name
 * @property string|null $address
 * @property string|null $email
 * @property string|null $phonenumber
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|PurchaseOrder[] $purchase_orders
 * @property Collection|Stockbatch[] $stockbatches
 * @property Collection|SupplierCreditPaymentHistory[] $supplier_credit_payment_histories
 *
 * @package App\Models
 */
class Supplier extends Model
{
    use LogsActivity;

	protected $table = 'supplier';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'address',
		'email',
		'phonenumber',
		'status'
	];

    public static $fields = [
        'name',
        'address',
        'email',
        'phonenumber',
        'status'
    ];


    public static $validate = [
        'name'=>'required',
        'phonenumber'=>'required',
    ];

	public function purchase_orders()
	{
		return $this->hasMany(PurchaseOrder::class);
	}

	public function stockbatches()
	{
		return $this->hasMany(Stockbatch::class);
	}

	public function supplier_credit_payment_histories()
	{
		return $this->hasMany(SupplierCreditPaymentHistory::class);
	}
}

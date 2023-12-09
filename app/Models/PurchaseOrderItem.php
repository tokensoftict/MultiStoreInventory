<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class PurchaseOrderItem
 * 
 * @property int $id
 * @property int $purchase_order_id
 * @property int $stock_id
 * @property int|null $stockbatch_id
 * @property Carbon|null $expiry_date
 * @property string|null $store
 * @property int $qty
 * @property string $purchase_order_invoice_number
 * @property string $type
 * @property float|null $cost_price
 * @property float|null $selling_price
 * @property int|null $added_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User|null $user
 * @property PurchaseOrder $purchase_order
 * @property Stock $stock
 * @property Stockbatch|null $stockbatch
 *
 * @package App\Models
 */
class PurchaseOrderItem extends Model
{

	protected $table = 'purchase_order_items';

	protected $casts = [
		'purchase_order_id' => 'int',
		'stock_id' => 'int',
		'stockbatch_id' => 'int',
		'qty' => 'int',
		'cost_price' => 'float',
        'selling_price' => 'float',
		'added_by' => 'int'
	];

	protected $dates = [
		'expiry_date'
	];

	protected $fillable = [
		'purchase_order_id',
		'stock_id',
        'supplier_id',
		'stockbatch_id',
		'expiry_date',
		'store',
		'qty',
		'cost_price',
        'selling_price',
		'added_by',
        'type',
        'purchase_order_invoice_number'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'added_by');
	}

	public function purchase_order()
	{
		return $this->belongsTo(PurchaseOrder::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function stockbatch()
	{
		return $this->belongsTo(Stockbatch::class);
	}
}

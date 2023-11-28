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
 * Class Stockbatch
 * 
 * @property int $id
 * @property Carbon|null $received_date
 * @property Carbon|null $expiry_date
 * @property int $quantity
 * @property int|null $supplier_id
 * @property int|null $stock_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock|null $stock
 * @property Supplier|null $supplier
 * @property Collection|InvoiceItemBatch[] $invoice_item_batches
 * @property Collection|PurchaseOrderItem[] $purchase_order_items
 * @property Collection|StockLogOperation[] $stock_log_operations
 *
 * @package App\Models
 */
class Stockbatch extends Model
{
	protected $table = 'stockbatches';

    use LogsActivity;

	protected $casts = [
		'quantity' => 'int',
		'supplier_id' => 'int',
		'stock_id' => 'int'
	];

	protected $dates = [
		'received_date',
		'expiry_date'
	];
    //protected $fillable = ['*'];

    protected $guarded = [];
/*
	protected $fillable = [
		'received_date',
		'expiry_date',
		'quantity',
		'supplier_id',
		'stock_id'
	];
*/
	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function supplier()
	{
		return $this->belongsTo(Supplier::class);
	}

	public function invoice_item_batches()
	{
		return $this->hasMany(InvoiceItemBatch::class);
	}

	public function purchase_order_items()
	{
		return $this->hasMany(PurchaseOrderItem::class);
	}

	public function stock_log_operations()
	{
		return $this->hasMany(StockLogOperation::class);
	}
}

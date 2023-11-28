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
 * Class InvoiceItem
 * 
 * @property int $id
 * @property int $invoice_id
 * @property int|null $stock_id
 * @property int $quantity
 * @property int|null $customer_id
 * @property string $status
 * @property int $added_by
 * @property Carbon $invoice_date
 * @property Carbon $sales_time
 * @property float $cost_price
 * @property float $selling_price
 * @property float $profit
 * @property float $total_cost_price
 * @property float $total_selling_price
 * @property float $total_profit
 * @property string|null $discount_type
 * @property string|null $store
 * @property float|null $discount_amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Customer|null $customer
 * @property Invoice $invoice
 * @property Stock|null $stock
 * @property Collection|InvoiceItemBatch[] $invoice_item_batches
 *
 * @package App\Models
 */
class InvoiceItem extends Model
{

    use LogsActivity;

	protected $table = 'invoice_items';

	protected $casts = [
		'invoice_id' => 'int',
		'stock_id' => 'int',
		'quantity' => 'int',
		'customer_id' => 'int',
		'added_by' => 'int',
        'warehousestore_id' => 'int',
		'cost_price' => 'float',
		'selling_price' => 'float',
		'profit' => 'float',
		'total_cost_price' => 'float',
		'total_selling_price' => 'float',
		'total_profit' => 'float',
		'discount_amount' => 'float'
	];

	protected $dates = [
		'invoice_date',
		'sales_time'
	];

	protected $fillable = [
		'invoice_id',
		'stock_id',
		'quantity',
		'customer_id',
        'warehousestore_id',
		'status',
		'added_by',
		'invoice_date',
		'sales_time',
		'cost_price',
        'department',
		'selling_price',
        'store',
		'profit',
		'total_cost_price',
		'total_selling_price',
		'total_profit',
		'discount_type',
		'discount_amount'
	];

    public function warehousestore()
    {
        return $this->belongsTo(Warehousestore::class);
    }

	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function invoice_item_batches()
	{
		return $this->hasMany(InvoiceItemBatch::class);
	}
}

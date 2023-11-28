<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class StockTransferItem
 * 
 * @property int $id
 * @property int $stock_transfer_id
 * @property int $stock_id
 * @property int|null $user_id
 * @property string|null $product_type
 * @property int $from
 * @property int $to
 * @property float $selling_price
 * @property float $cost_price
 * @property int $quantity
 * @property Carbon $transfer_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Warehousestore $warehousestore
 * @property Stock $stock
 * @property StockTransfer $stock_transfer
 * @property User|null $user
 *
 * @package App\Models
 */
class StockTransferItem extends Model
{
    use LogsActivity;

	protected $table = 'stock_transfer_items';

	protected $casts = [
		'stock_transfer_id' => 'int',
		'stock_id' => 'int',
		'user_id' => 'int',
		'from' => 'int',
		'to' => 'int',
		'selling_price' => 'float',
		'cost_price' => 'float',
		'quantity' => 'int'
	];

	protected $dates = [
		'transfer_date'
	];

	protected $fillable = [
		'stock_transfer_id',
		'stock_id',
		'user_id',
		'product_type',
		'from',
		'to',
		'selling_price',
		'cost_price',
		'quantity',
		'transfer_date'
	];

    public function store_from()
    {
        return $this->belongsTo(Warehousestore::class, 'from');
    }

	public function store_to()
	{
		return $this->belongsTo(Warehousestore::class, 'to');
	}

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function stock_transfer()
	{
		return $this->belongsTo(StockTransfer::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

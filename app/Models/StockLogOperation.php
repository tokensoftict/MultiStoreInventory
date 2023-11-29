<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class StockLogOperation
 * 
 * @property int $id
 * @property int $stock_id
 * @property int|null $user_id
 * @property int $stockbatch_id
 * @property float $selling_price
 * @property float $cost_price
 * @property string $operation_type
 * @property int $operation_id
 * @property int $quantity
 * @property string $store
 * @property Carbon $log_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Stock $stock
 * @property Stockbatch $stockbatch
 * @property User|null $user
 *
 * @package App\Models
 */
class StockLogOperation extends Model
{

	protected $table = 'stock_log_operations';

	protected $casts = [
		'stock_id' => 'int',
		'user_id' => 'int',
		'stockbatch_id' => 'int',
		'selling_price' => 'float',
		'cost_price' => 'float',
		'operation_id' => 'int',
		'quantity' => 'int'
	];

	protected $dates = [
		'log_date'
	];

	protected $fillable = [
		'stock_id',
		'user_id',
		'stockbatch_id',
		'selling_price',
		'cost_price',
		'operation_type',
		'operation_id',
		'quantity',
		'store',
		'log_date'
	];

	public function stock()
	{
		return $this->belongsTo(Stock::class);
	}

	public function stockbatch()
	{
		return $this->belongsTo(Stockbatch::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function operation()
    {
	    $this->morphTo();
    }

    public function returnStockBack()
    {
        $this->stockbatch->{$this->store} += $this->quantity;

        $this->stockbatch->update();
    }

    public function minusStockBack()
    {
        $this->stockbatch->{$this->store} -= $this->quantity;

        $this->stockbatch->update();
    }

    public static function createOperationLog($data)
    {
	    return StockLogOperation::create($data);
    }

}

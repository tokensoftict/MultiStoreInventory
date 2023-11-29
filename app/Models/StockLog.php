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
 * Class StockLog
 *
 * @property int $id
 * @property string $log_type
 * @property string $product_type
 * @property int|null $user_id
 * @property Carbon $log_date
 * @property float $total_worth
 * @property string|null $usage_type
 * @property string|null $department
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User|null $user
 * @property Collection|StockLogItem[] $stock_log_items
 *
 * @package App\Models
 */
class StockLog extends Model
{

    protected $table = 'stock_logs';

    protected $casts = [
        'user_id' => 'int',
        'total_worth' => 'float'
    ];

    protected $dates = [
        'log_date'
    ];

    protected $fillable = [
        'log_type',
        'user_id',
        'log_date',
        'total_worth',
        'usage_type',
        'product_type',
        'department'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stock_log_items()
    {
        return $this->hasMany(StockLogItem::class);
    }


    public function stockLogOperation()
    {
        $this->morphMany(StockLogOperation::class,'operation');
    }


    public function createStockLog($request){

        $store_column = getActiveStore();

        $log =StockLog::create([
            'log_type' => $request->usage_type,
            'department' => $request->department,
            'user_id' => auth()->id(),
            'product_type' => $request->product_type,
            'log_date' => $request->date_created,
            'total_worth' => 0,
            'usage_type' => $request->usage_type,
        ]);

        $stocks = $request->get('stock_id');
        $qty = $request->get('qty');
        $cost_price = $request->get('cost_price');
        $selling_price = $request->get('selling_price');
        $total = 0;

        $select_stocks =  Stock::whereIn('id', array_keys($stocks))->get();



        $log->total_worth = $total;
        $log->update();

        return redirect()->route('stocklog.add_log')->with('success','Stock Log has been created successfully!');
    }



}

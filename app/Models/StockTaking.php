<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StockTaking
 * 
 * @property int $id
 * @property string $name
 * @property int $warehousestore_id
 * @property int|null $user_id
 * @property string $status
 * @property Carbon $date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Warehousestore $warehousestore
 * @property Collection|StockTakingItem[] $stock_taking_items
 *
 * @package App\Models
 */
class StockTaking extends Model
{
	protected $table = 'stock_takings';

	protected $casts = [
		'warehousestore_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'date'
	];

	protected $fillable = [
		'name',
		'warehousestore_id',
		'user_id',
		'status',
		'date'
	];

	public static $validation = [
	    'name' => 'required',
        'warehousestore_id'=> 'required',
        'date' =>'required',
    ];

	public static $fields = [
        'name',
        'warehousestore_id',
        'user_id',
        'status',
        'date'
    ];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function warehousestore()
	{
		return $this->belongsTo(Warehousestore::class);
	}

	public function stock_taking_items()
	{
		return $this->hasMany(StockTakingItem::class);
	}


	public static function createStockTaking($request)
    {
        $request->validate(StockTaking::$validation);

        $data = $request->only(StockTaking::$fields);

        $data['user_id'] = auth()->id();

        $stockTaking = StockTaking::create($data);


        return redirect()->route('counting.index')->with('success','Stock Counting has been created successfully!..');
    }

}

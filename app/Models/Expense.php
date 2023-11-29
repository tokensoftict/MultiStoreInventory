<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Expense
 *
 * @property int $id
 * @property float $amount
 * @property string $department
 * @property int|null $warehousestore_id
 * @property int|null $expenses_type_id
 * @property int|null $user_id
 * @property Carbon $expense_date
 * @property string|null $purpose
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property ExpensesType|null $expenses_type
 * @property User|null $user
 * @property Warehousestore|null $warehousestore
 *
 * @package App\Models
 */
class Expense extends Model
{
    protected $table = 'expenses';

    protected $casts = [
        'amount' => 'float',
        'warehousestore_id' => 'int',
        'expenses_type_id' => 'int',
        'user_id' => 'int'
    ];

    protected $dates = [
        'expense_date'
    ];

    protected $fillable = [
        'amount',
        'department',
        'warehousestore_id',
        'expenses_type_id',
        'user_id',
        'expense_date',
        'purpose'
    ];

    public static $validate = [
        'amount' => 'required',
        'warehousestore_id' =>    'required',
        'expenses_type_id' =>    'required',
        'expense_date' =>    'required',
    ];


    public static $fields = [
        'amount',
        'department',
        'warehousestore_id',
        'expenses_type_id',
        'expense_date',
        'purpose'
    ];

    public function expenses_type()
    {
        return $this->belongsTo(ExpensesType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehousestore()
    {
        return $this->belongsTo(Warehousestore::class);
    }
}

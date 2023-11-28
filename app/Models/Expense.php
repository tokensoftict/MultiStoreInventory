<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Expense
 * 
 * @property int $id
 * @property float $amount
 * @property int|null $expenses_type_id
 * @property int|null $user_id
 * @property Carbon $expense_date
 * @property string|null $purpose
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property ExpensesType|null $expenses_type
 * @property User|null $user
 *
 * @package App\Models
 */
class Expense extends Model
{
    use LogsActivity;

	protected $table = 'expenses';

	protected $casts = [
		'amount' => 'float',
		'expenses_type_id' => 'int',
		'user_id' => 'int'
	];

	protected $dates = [
		'expense_date'
	];

	protected $fillable = [
		'amount',
		'expenses_type_id',
		'user_id',
        'department',
		'expense_date',
		'purpose'
	];


    public static $fields = [
        'amount',
        'expenses_type_id',
        'user_id',
        'department',
        'expense_date',
        'purpose'
    ];

	public static $validate = [
	  'amount'=>"required",
      "expenses_type_id" => "required",
      "department" => "required",
      "expense_date" => "required",
    ];

	public function expenses_type()
	{
		return $this->belongsTo(ExpensesType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}

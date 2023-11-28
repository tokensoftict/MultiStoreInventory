<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Cashbook
 * 
 * @property int $id
 * @property int|null $last_updated
 * @property int|null $user_id
 * @property float $amount
 * @property Carbon $transaction_date
 * @property string $type
 * @property int $bank_account_id
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property BankAccount $bank_account
 * @property User|null $user
 *
 * @package App\Models
 */
class Cashbook extends Model
{
    use LogsActivity;

	protected $table = 'cashbook';

	protected $casts = [
		'last_updated' => 'int',
		'user_id' => 'int',
		'amount' => 'float',
		'bank_account_id' => 'int'
	];

	protected $dates = [
		'transaction_date'
	];

	protected $fillable = [
		'last_updated',
		'user_id',
		'amount',
		'transaction_date',
		'type',
		'bank_account_id',
		'comment'
	];

	public static $fields = [
        'last_updated',
        'user_id',
        'amount',
        'transaction_date',
        'type',
        'bank_account_id',
        'comment'
    ];

	public static $validation =[
	  'type'=>'required',
      'bank_account_id'=>'required',
      'amount' => 'required'
    ];


	public function bank_account()
	{
		return $this->belongsTo(BankAccount::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function last_update()
    {
        return $this->belongsTo(User::class,'last_updated');
    }
}

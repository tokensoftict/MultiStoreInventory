<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class BankAccount
 * 
 * @property int $id
 * @property int $bank_id
 * @property string|null $account_number
 * @property string|null $account_name
 * @property bool $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Bank $bank
 *
 * @package App\Models
 */
class BankAccount extends Model
{



	protected $table = 'bank_accounts';

	protected $casts = [
		'bank_id' => 'int',
		'status' => 'bool'
	];

	protected $fillable = [
		'bank_id',
		'account_number',
		'account_name',
		'status'
	];

	public static $fields = [
        'bank_id',
        'account_number',
        'account_name',
        'status'
    ];

    public static $validate = [
        'bank_id'=>'required',
        'account_number'=>'required',
        'account_name'=>'required',
    ];

	public function bank()
	{
		return $this->belongsTo(Bank::class);
	}
}

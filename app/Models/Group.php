<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Group
 * 
 * @property int $id
 * @property string $name
 * @property bool $status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Permission[] $permissions
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class Group extends Model
{
	use SoftDeletes;



	protected $table = 'groups';

	protected $casts = [
		'status' => 'bool'
	];

	protected $fillable = [
		'name',
		'status'
	];

	public function permissions()
	{
		return $this->hasMany(Permission::class);
	}

    public static $rules = [
        'name' => 'required|string|unique:groups',
    ];

    public static $rules_update = [
        'name' => 'sometimes|required|string',
    ];


    public function tasks()
    {
        return $this->belongstoMany(Task::class, 'permissions');
    }

	public function users()
	{
		return $this->hasMany(User::class);
	}

    public function group_tasks()
    {
        return $this->belongsToMany(Task::class, 'permissions')->withTimestamps();
    }
}

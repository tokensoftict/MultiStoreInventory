<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super_admin_tasks = DB::table('permissions')->where('group_id',1)->pluck('task_id');
        $new_tasks = DB::table('tasks')->whereNotIn('id',$super_admin_tasks)->pluck('id');
        foreach ($new_tasks as $new_task){
            DB::table('permissions')
                ->insert([
                    'task_id'=>$new_task,
                    'group_id'=>1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }
    }
}

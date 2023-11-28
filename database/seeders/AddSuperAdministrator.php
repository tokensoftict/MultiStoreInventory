<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSuperAdministrator extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $normal_group = Group::find(1);

        $normal_group->name = "Super Administrator";

        $normal_group->update();

        $groups = [
            'System Administrator'
        ];

        $_insert = [];
        foreach ($groups as $group) {
            $_insert[] = ['name' => $group, 'status' => '1','created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
        }

        DB::table('groups')->insert($_insert);

        $users = User::where('group_id',1)->get();

        foreach ($users as $user)
        {
            $user->group_id = 2;
            $user->update();
        }

        $super_admin_tasks = DB::table('permissions')->where('group_id',2)->pluck('task_id');
        $new_tasks = DB::table('tasks')->whereNotIn('id',$super_admin_tasks)->pluck('id');
        foreach ($new_tasks as $new_task){
            DB::table('permissions')
                ->insert([
                    'task_id'=>$new_task,
                    'group_id'=>2,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }


        //now lest create a superadmin account

        DB::table('users')->insert([
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@store.com',
                'username'=>'superadmin',
                'group_id'=>1,
                'email_verified_at'=>Carbon::now(),
                'password' => bcrypt('superadmin'),
                'status' => '1',
                "customer_type" => "  ",
                "customer_id" => "0",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);

    }
}

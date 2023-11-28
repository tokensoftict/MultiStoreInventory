<?php

namespace Database\Seeders;


use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Administrator',
                'email' => 'admin@store.com',
                'username'=>'admin',
                'group_id'=>1,
                'email_verified_at'=>Carbon::now(),
                'password' => bcrypt('admin'),
                'status' => '1',
                "customer_type" => "  ",
                "customer_id" => "0",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }
}

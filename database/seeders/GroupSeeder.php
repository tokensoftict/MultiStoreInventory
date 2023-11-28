<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            'System Administrator'
        ];

        $_insert = [];
        foreach ($groups as $group) {
            $_insert[] = ['name' => $group, 'status' => '1','created_at' => Carbon::now(), 'updated_at' => Carbon::now()];
        }

        DB::table('groups')->insert($_insert);
    }
}

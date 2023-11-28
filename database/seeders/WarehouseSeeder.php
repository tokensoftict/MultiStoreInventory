<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warehousestores')->insert([
            'name'=>'Store',
            'packed_column'=>'quantity',
            'yard_column'=>'yard_quantity',
            'type'=>'SHOP',
            'default'=>1,
            'status'=>1
        ]);

        DB::table('userstoremappers')->insert(['user_id' => 1, 'warehousestore_id' => 1]);

        getActiveStore(true);

    }
}

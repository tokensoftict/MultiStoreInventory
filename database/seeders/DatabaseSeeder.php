<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call(BanksSeeder::class);
       $this->call(GroupSeeder::class);
       Artisan::call('task:generate');
       $this->call(PermissionSeeder::class);
        $this->call(CustomerSeeder::class);
       $this->call(StatusSeeder::class);
       $this->call(PaymentMethodSeeder::class);
       $this->call(UserSeeder::class);
        $this->call(WarehouseSeeder::class);
    }
}

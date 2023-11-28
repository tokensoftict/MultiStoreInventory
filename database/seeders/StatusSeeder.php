<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
            [
                'name'=>'Active',
                'label'=>'primary'
            ],
            [
                'name'=>'Occupied',
                'label'=>'warning'
            ],
            [
                'name'=>'Paid',
                'label'=>'success'
            ],
            [
                'name'=>'Draft',
                'label'=>'primary'
            ],
            [
                'name'=>'Checked-out',
                'label'=>'success'
            ],
            [
                'name'=>'Partial Payment',
                'label'=>'primary'
            ],
            [
                'name'=>'Pending',
                'label'=>'warning'
            ],
            [
                'name'=>'Uploaded',
                'label'=>'primary'
            ],
            [
                'name'=>'Complete',
                'label'=>'success'
            ],
        ]);
    }
}

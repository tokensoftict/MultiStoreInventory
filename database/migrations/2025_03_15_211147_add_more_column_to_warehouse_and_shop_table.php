<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreColumnToWarehouseAndShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehousestores', function (Blueprint $table) {
            $table->string('first_address', 150)->after('status')->nullable();
            $table->string('second_address', 150)->after('status')->nullable();
            $table->string('contact_number', 50)->after('status')->nullable();
            $table->string('footer_notes', 300)->after('status')->nullable();
            $table->string('logo', 100)->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehousestores', function (Blueprint $table) {
            $table->dropColumn(['first_address', 'second_address', 'contact_number', 'footer_notes', 'logo']);
        });
    }
}

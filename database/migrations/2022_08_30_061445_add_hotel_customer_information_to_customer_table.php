<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHotelCustomerInformationToCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('passport_no')->nullable()->after('phone_number');
            $table->string('passport_expire_date')->nullable()->after('phone_number');
            $table->string('nationality')->nullable()->after('phone_number');
            $table->string('nok_firstname')->nullable()->after('phone_number');
            $table->string('nok_lastname')->nullable()->after('phone_number');
            $table->string('nok_email')->nullable()->after('phone_number');
            $table->string('nok_phone_number')->nullable()->after('phone_number');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['passport_no','passport_expire_date','nationality','nok_firstname','nok_lastname','nok_email','nok_phone_number']);
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerInfomationToHotelBooking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {

            $table->string('occupation')->nullable()->after('nok_phone_number');
            $table->string('purpose_of_visit')->nullable()->after('occupation');
            $table->string('vehicle_reg_number')->nullable()->after('occupation');
            $table->string('arriving_from')->nullable()->after('occupation');

            $table->string('city')->nullable()->after('arriving_from');
            $table->string('state')->nullable()->after('vehicle_reg_number');

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
            $table->dropColumn(['occupation','purpose_of_visit','vehicle_reg_number','arriving_from','city','state']);
        });
    }
}

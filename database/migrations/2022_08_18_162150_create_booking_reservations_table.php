<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number');
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('status_id')->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('customer_identity');
            $table->date('booking_date');
            $table->decimal('total');
            $table->decimal('total_paid');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_reservations');
    }
}

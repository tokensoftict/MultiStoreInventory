<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceScanDateAndInvoiceScanByToInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger("scan_user_id")->nullable()->after('voided_by');
            $table->date("scan_date")->nullable()->after('scan_user_id');
            $table->time("scan_time")->nullable()->after('scan_date');
            $table->foreign('scan_user_id')->references('id')->on('users')->nullOnDelete();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('invoices_scan_user_id_foreign');
            $table->dropColumn(['scan_date', 'scan_time', 'scan_user_id']);
        });
    }
}

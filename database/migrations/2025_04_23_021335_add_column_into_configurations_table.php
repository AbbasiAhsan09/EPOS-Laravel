<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean("show_bag_sizing")->default(false)->nullable();
            $table->boolean("cash_printer_thermal")->nullable()->default(false);
            $table->string("credit_printer")->nullable();
            $table->string("receipt_printer")->nullable();
            $table->boolean("show_ad_on_invoice")->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn("show_bag_sizing");
            $table->dropColumn("cash_printer_thermal");
            $table->dropColumn("credit_printer");
            $table->dropColumn("receipt_printer");
        });
    }
};

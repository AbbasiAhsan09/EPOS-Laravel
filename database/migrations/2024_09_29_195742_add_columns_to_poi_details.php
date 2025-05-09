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
        Schema::table('purchase_invoice_details', function (Blueprint $table) {
            $table->decimal('bags',15,2)->nullable();
            $table->decimal('bag_size',15,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_invoice_details', function (Blueprint $table) {
            $table->dropColumn(['bags','bag_size']);
        });
    }
};

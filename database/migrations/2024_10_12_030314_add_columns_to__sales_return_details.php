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
        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->decimal("bags",11,2)->default(0);
            $table->decimal("bag_size",11,2)->default(0);
            $table->decimal('original_qty',11,2)->change()->default(0);
            $table->decimal('original_rate',11,2)->change()->default(0);
            $table->decimal('original_tax',11,2)->change()->default(0);
            $table->decimal('original_disc',11,2)->change()->default(0);
            $table->decimal('original_total',11,2)->change()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->dropColumn(['bags','bag_size']);
        });
    }
};

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
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->string('invoice_no')->nullable();
            $table->enum("discount_type",["FLAT",'PERCENT'])->default('PERCENT');
            $table->decimal('discount',11,2)->default(0);
            $table->decimal('net_total',11,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sale_returns', function (Blueprint $table) {
            $table->dropColumn(["invoice_no","discount_type","discount","net_total"]);
        });
    }
};

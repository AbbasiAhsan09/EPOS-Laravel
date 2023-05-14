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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_opnening_stock')->default(0);
            $table->integer('item_id');
            // $table->integer('po_id')->nullable();
            $table->decimal('wght_cost')->default(0);
            $table->decimal('stock_qty',50,2)->default(0); //Stock Qty will be calculated as base unit
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventories');
    }
};

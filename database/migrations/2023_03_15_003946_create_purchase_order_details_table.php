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
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_id')->constrained('purchase_orders');
            $table->foreignId('item_id')->constrained('products');
            $table->decimal('qty',50,2);
            $table->decimal('rate',50,2);
            $table->decimal('tax',50,2);
            $table->decimal('discount',3,2)->default(0); // In Percentage
            $table->decimal('total',50,2);
            $table->integer('status')->default(1);
            $table->boolean('is_base_unit');
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
        Schema::dropIfExists('purchase_order_details');
    }
};

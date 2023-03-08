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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales');
            $table->foreignId('item_id')->constrained('products');
            $table->boolean('is_base_unit');
            $table->integer('batch_id')->nullable();
            $table->decimal('rate',50,2);
            $table->decimal('tax',50,2);
            $table->decimal('qty',50,2);
            $table->decimal('disc',50,2)->default(0);
            $table->decimal('total',50,2);
            $table->integer('status')->default(1);
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
        Schema::dropIfExists('sales_details');
    }
};

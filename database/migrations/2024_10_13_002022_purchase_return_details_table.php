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
       Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_return_id')->constrained('purchase_returns');
            $table->foreignId('item_id')->constrained('products');
            $table->decimal('original_qty')->default(0);
            $table->boolean('is_base_unit')->default(0);
            $table->decimal('returned_qty')->default(0);
            $table->integer('batch_id')->nullable();
            $table->decimal('original_rate',11,2)->default(0);
            $table->decimal('returned_rate',11,2);
            $table->decimal('original_tax',11,2)->default(0);
            $table->decimal('returned_tax',11,2)->default(0);
            $table->decimal('original_disc',11,2)->default(0);
            $table->decimal('returned_disc',11,2)->default(0);
            $table->decimal('original_total',11,2)->default(0);
            $table->decimal('returned_total',11,2)->default(0);
            $table->integer('status')->default(1);
            $table->decimal("bags",11,2)->default(0);
            $table->decimal("bag_size",11,2)->default(0);
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
        Schema::dropIfExists('purchase_return_details');
    }
};

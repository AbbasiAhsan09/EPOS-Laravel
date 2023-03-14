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
        Schema::create('purchase_quotation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('purchase_quotations');
            $table->foreignId('item_id')->constrained('products');
            $table->decimal('qty',50,2)->default(0);
            $table->decimal('rate',50,2);
            $table->decimal('total',50,2);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('purchase_quotation_details');
    }
};

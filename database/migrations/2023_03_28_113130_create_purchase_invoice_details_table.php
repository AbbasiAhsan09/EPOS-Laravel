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
        Schema::create('purchase_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inv_id')->constrained('purchase_invoices');
            $table->foreignId('item_id')->constrained('products');
            $table->decimal('qty',50,2)->default(0);
            $table->decimal('rate',50,2);
            $table->decimal('total',50,2);
            $table->decimal('tax',50,2)->default(0);
            $table->decimal('disc',50,2)->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('purchase_invoice_details');
    }
};

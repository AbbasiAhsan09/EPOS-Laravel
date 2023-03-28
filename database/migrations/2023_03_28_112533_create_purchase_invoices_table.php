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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('doc_num');
            $table->foreignId('party_id')->constrained('parties');
            $table->foreignId('po_id')->constrained('purchase_orders');
            $table->decimal('total',50,2);
            $table->decimal('discount',50,2);
            $table->enum('discount_type' ,['PERCENT','FLAT'])->default('PERCENT');
            $table->decimal('tax',50,2);
            $table->decimal('shipping',50,2);
            $table->decimal('others',50,2)->default(0);
            $table->decimal('net_amount',50,2);
            $table->foreignId('created_by')->constrained('users');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('purchase_invoices');
    }
};

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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('doc_num');
            $table->foreignId('party_id')->constrained('parties');
            $table->string('quotation_num')->nullable();
            $table->enum('type',['STANDARD','CONTRACT','PLANNED','STANDING'])->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->decimal('sub_total',50,2);
            $table->decimal('discount')->default(0);
            $table->enum('discount_type',['PERCENT','FLAT'])->default('PERCENT');
            $table->decimal('shipping_cost',50,2)->default(0);
            $table->decimal('tax',50,2)->default(0);
            $table->integer('status')->default(1); //0 cancelled order
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
        Schema::dropIfExists('purchase_orders');
    }
};

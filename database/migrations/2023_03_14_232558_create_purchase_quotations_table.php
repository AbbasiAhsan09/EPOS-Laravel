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
        Schema::create('purchase_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('doc_num');
            $table->string('req_num')->nullable();
            $table->foreignId('party_id')->constrained('parties');
            $table->enum('type',['PURCHASE','SALES'])->default('PURCHASE');
            $table->string('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->integer('status')->default(1);
            $table->decimal('other_charges',50,2);
            $table->decimal('gross_total',50,2);
            $table->decimal('discount',50,2);
            $table->enum('discount_type',['PERCENT','FLAT']);
            $table->decimal('net_total',50,2);
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
        Schema::dropIfExists('purchase_quotations');
    }
};

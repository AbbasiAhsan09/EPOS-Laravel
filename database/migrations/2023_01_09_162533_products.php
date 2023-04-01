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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('category');
            $table->integer('arrt_it')->nullable();
            $table->decimal('opening_stock',50,2)->default(0);
            $table->integer('low_stock')->default(20);
            $table->string('barcode')->nullable()->unique();
            $table->integer('uom')->default(0);
            $table->decimal('mrp',10,2)->default(0.00);
            $table->decimal('tp',10,2)->default(0.00);
            $table->decimal('discount',10,2)->default(0.00);
            $table->decimal('taxes')->default(0.00);
            $table->integer('store_id');
            $table->string('img')->nullable();
            $table->string('brand')->nullable();
            $table->string('description')->nullable();
            $table->integer('status')->default(1);
            $table->integer('addedBy')->default(0);
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
        //
    }
};

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
        Schema::create('purchase_request_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('purchase_requests');
            $table->foreignId('item_id')->constrained('products');
            $table->decimal('rate',50,2)->default(0);
            $table->decimal('qty',50,2)->default(0);
            $table->decimal('total',50,2)->default(0);
            $table->decimal('taxes',50,2)->default(0);
            $table->boolean('is_base_unit')->default(0);
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
        Schema::dropIfExists('purchase_request_details');
    }
};

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
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id('id');
            $table->integer('order_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('customer_id')->nullable();
            $table->enum('status',['recieved','paid']);
            $table->decimal('amount',50,2);
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });


        // Schema::table('order_transactions', function(Blueprint $table){
        //     $table->foreign('order_id')->references('id')->on('sales')->onDelete('cascade');
        //     $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_transactions');
    }
};

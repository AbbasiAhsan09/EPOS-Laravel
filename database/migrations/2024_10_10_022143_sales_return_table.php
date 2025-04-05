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
        Schema::create('sale_returns', function (Blueprint $table) {
            $table->id();
            $table->integer('store_id')->nullable();
            $table->string('doc_no');
            $table->integer('user_id')->nullable();
            $table->integer('sale_id')->nullable();
            $table->integer('party_id')->nullable();
            $table->date('return_date')->nullable();
            $table->text('reason')->nullable();
            $table->decimal('total',11,2);
            $table->decimal('other_charges',11,2)->default(0);
            $table->decimal('refunded_amount',11,2)->default(0);
            // $table->boolean('status')->default(false);
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
        Schema::dropIfExists('sale_returns');
    }
};

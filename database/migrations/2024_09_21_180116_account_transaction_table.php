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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('store_id');
            $table->integer('account_id');
            $table->string('reference_type')->nullable();
            $table->integer('reference_id')->nullable();
            $table->decimal('credit',15,2)->nullable();
            $table->decimal('debit',15,2)->nullable();
            $table->text('note')->nullable();
            $table->date('transaction_date');
            $table->integer('recorded_by')->nullable();
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
        Schema::dropIfExists('account_transactions');
    }
};

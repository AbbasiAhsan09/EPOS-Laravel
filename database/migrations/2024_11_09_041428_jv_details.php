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
        Schema::create('journal_voucher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_voucher_id')->constrained('journal_vouchers');
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('store_id')->constrained('stores');
            $table->string("reference_no")->nullable();
            $table->string("description")->nullable();
            $table->enum("mode",["cash","cheque","credit_card","offset","online","pay_order"])->nullable();
            $table->decimal('debit',11,2)->default(0);
            $table->decimal('credit',11,2)->default(0);
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

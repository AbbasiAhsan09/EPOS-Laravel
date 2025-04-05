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
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no');
            $table->foreignId('account_id')->constrained('accounts');
            $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('user_id')->constrained('users');
            $table->string("reference_no")->nullable();
            $table->date("date");
            $table->enum("mode",["cash","cheque","credit_card","offset","online","pay_order"])->nullable();
            $table->decimal('total_credit',11,2)->default(0);
            $table->decimal('total_debit',11,2)->default(0);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('journal_vouchers');
    }
};

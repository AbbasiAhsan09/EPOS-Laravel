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
        Schema::table('configurations', function (Blueprint $table) {
            // $table->enum('invoice_type',['web','thermal'])->default('thermal')->change();
            $table->enum('invoice_template', ['invoice1','invoice2','invoice3'])->default('invoice1');
            $table->boolean('send_invoice_on_order')->default(0);
            $table->boolean('print_invoice_on_order')->default(1);
            $table->boolean('send_updates_to_admin')->default(0);
            $table->string('date_format')->default('d-m-y');
            $table->string('time_format')->default('h:m:s A');
            $table->boolean('print_title_on_invoice')->default(0);
            $table->boolean('paid_stamp_on_paid_invoice')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('remarks')->nullable();
            $table->integer('customer_id')->default(0);
            $table->decimal('gross_total',50,2);
            $table->decimal('vat',50,2);
            $table->decimal('gst',50,2);
            $table->decimal('advance_tax')->default(0);
            $table->decimal('other_tax',50,2)->default(0);
            $table->decimal('other_charges',50,2)->default(0);
            $table->decimal('net_total',50,2)->default(0);
            $table->integer('status')->default(1);
            $table->decimal('recieved')->default(0);
            $table->integer('user_id');
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
        Schema::dropIfExists('sales');
    }
};

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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('app_title');
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('ntn')->nullable();
            $table->string('ptn')->nullable();
            $table->boolean('show_ntn')->default(0);
            $table->boolean('show_ptn')->default(0);
            $table->boolean('inventory_tracking')->default(1);
            $table->boolean('mutltiple_sales_order')->default(1);
            $table->date('start_date');
            $table->integer('contract_duration')->default(12); //months
            $table->date('renewed_on')->nullable();
            $table->text('invoice_message')->nullable();
            $table->text('inv_dev_message')->nullable();
            $table->string('dev_contact')->nullable();
            $table->foreignId('added_by')->constrained('users');
            $table->integer('updated_by')->nullable();
            $table->integer('invoice_type')->default(1);
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
        Schema::dropIfExists('configurations');
    }
};

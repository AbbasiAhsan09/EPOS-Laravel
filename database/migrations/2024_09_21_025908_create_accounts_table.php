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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('pre_defined')->default(false);
            $table->enum('type',['assets','expenses','income','equity','liabilities']);
            $table->integer('store_id')->nullable();
            $table->string('description')->nullable();
            $table->string('color_code')->nullable();
            $table->integer('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->decimal('opening_balance',15,2)->nullable();
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
        Schema::dropIfExists('accounts');
    }
};

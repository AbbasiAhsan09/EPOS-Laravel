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
        Schema::create('app_form_fields', function (Blueprint $table) {
            $table->id();
            $table->integer('form_id');
            $table->string('label');
            $table->string('name');
            $table->enum('datatype',['string','integer','boolean']);
            $table->enum('type',['input','textarea','select','checkbox','radio']);
            $table->boolean('required')->default(0);
            $table->integer('store_id')->nullable();
            $table->integer('by_user')->nullable();
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
        Schema::dropIfExists('app_form_fields');
    }
};

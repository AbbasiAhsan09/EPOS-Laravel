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
        Schema::create('keyboard_shortcuts', function (Blueprint $table) {
            $table->id();
            $table->string("description")->nullable();
            $table->string("key");
            $table->string("action_type")->default("route");
            $table->string("uri")->nullable();
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
        Schema::dropIfExists('keyboard_shortcuts');
    }
};

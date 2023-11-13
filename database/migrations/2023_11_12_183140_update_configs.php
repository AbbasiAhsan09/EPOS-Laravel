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
        Schema::table('configurations', function(Blueprint $table) {
            $table->boolean('order_processing')->default(0);
            $table->integer('order_processing_template')->default(0); // 0 = [pending, proccedd, delivered, returned], 1 = [pending, shipped, delivered, returned] 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configurations', function(Blueprint $table) {
            $table->dropColumn(['order_processing','order_processing_template']);
        });
    }
};

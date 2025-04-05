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
        Schema::create('labour_work_history_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("labour_work_history_id")->constrained("labour_work_histories");
            $table->date("date");
            $table->string("description")->nullable();
            $table->decimal("rate",11,2);
            $table->decimal("qty",11,2);
            $table->decimal("total",11,2);
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
        Schema::dropIfExists("labour_work_history_items");
    }
};

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
        Schema::create('labour_work_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('labour_id')->constrained('labours');
            $table->date("start_date");
            $table->date("end_date")->nullable();
            $table->boolean('is_ended')->default(0);
            $table->boolean("is_paid")->default(0);
            $table->date("paid_date")->nullable();
            $table->decimal("net_total",11,2)->default(0);
            $table->foreignId('store_id')->constrained("stores");
            $table->foreignId("user_id")->nullable()->constrained('users');
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
        Schema::dropIfExists('labour_work_histories');
    }
};

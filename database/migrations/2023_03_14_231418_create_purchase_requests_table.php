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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('users');
            $table->integer('status')->default(0); // 0 unapproved 1 approved 2 rejected
            $table->enum('type',['STANDARD','CONTRACT','PLANNED','STANDING'])->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->integer('approved_by')->nullable();
            $table->string('remarks')->nullable();
            $table->date('required_on')->nullable();
            $table->decimal('total_amount',50,2);
            $table->integer('store_id')->nullable();
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
        Schema::dropIfExists('purchase_requests');
    }
};

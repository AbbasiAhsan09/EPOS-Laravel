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
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->foreignId('convert_to_unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->decimal('conversion_rate', 10, 4)->default(1);
            $table->decimal("unit_rate", 50, 4)->nullable();
            $table->decimal("unit_cost", 50, 4)->nullable();
            $table->string('unit_barcode')->nullable();
            $table->boolean('default')->default(false);
            $table->string('description')->nullable();
            $table->string('symbol')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
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
        //
    }
};

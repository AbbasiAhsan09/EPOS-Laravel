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

        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->string('description')->nullable();
            $table->boolean('is_base')->default(false);
            $table->foreignId('conversion_unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->decimal('default_conversion_factor', 20, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('unit_type_id')->nullable()->constrained('unit_types')->onDelete('set null');
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null');
            $table->boolean('pre_defined')->default(false);
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
        Schema::dropIfExists('units');
        Schema::dropIfExists('unit_types');
    }
};

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
        Schema::table('product_units', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->after("unit_barcode")->comment('Is Active');
            // $table->foreignId('conversion_unit_id')->after('unit_id')->nullable()->constrained('units')->onDelete('cascade')->comment('Conversion Unit of Measurement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_units', function (Blueprint $table) {
            // $table->dropForeign(['conversion_unit_id']);
            // $table->dropColumn('conversion_unit_id');
            $table->dropColumn('is_active');
        });
    }
};

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
        Schema::table('sales_details', function (Blueprint $table) {
            $table->foreignId('unit_id')->after('item_id')->nullable()->constrained('units')->onDelete('cascade')->comment('Unit of Measurement');
            $table->decimal('unit_conversion_rate', 20, 4)->nullable()->default(1)->comment('Unit Conversion Rate');
        });

        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->foreignId('unit_id')->after('item_id')->nullable()->constrained('units')->onDelete('cascade')->comment('Unit of Measurement');
            $table->decimal('unit_conversion_rate', 20, 4)->nullable()->default(1)->comment('Unit Conversion Rate');
        });

        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->foreignId('unit_id')->after('item_id')->nullable()->constrained('units')->onDelete('cascade')->comment('Unit of Measurement');
            $table->decimal('unit_conversion_rate', 20, 4)->nullable()->default(1)->comment('Unit Conversion Rate');
        });

        Schema::table('purchase_invoice_details', function (Blueprint $table) {
            $table->foreignId('unit_id')->after('item_id')->nullable()->constrained('units')->onDelete('cascade')->comment('Unit of Measurement');
            $table->decimal('unit_conversion_rate', 20, 4)->nullable()->default(1)->comment('Unit Conversion Rate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_details', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->dropColumn('unit_conversion_rate');
        });

        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->dropColumn('unit_conversion_rate');
        });

        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->dropColumn('unit_conversion_rate');
        });

        Schema::table('purchase_invoice_details', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->dropColumn('unit_conversion_rate');
        });
    }
};

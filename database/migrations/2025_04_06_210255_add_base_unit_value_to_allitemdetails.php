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
            $table->decimal('base_unit_value', 10, 2)->default(1)->after('is_base_unit')->comment('Base unit value');
        });

        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->decimal('base_unit_value', 10, 2)->default(1)->after('is_base_unit')->comment('Base unit value');
        });

        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->decimal('base_unit_value', 10, 2)->default(1)->after('is_base_unit')->comment('Base unit value');
        });

        Schema::table('purchase_invoice_details', function (Blueprint $table) {
            $table->decimal('base_unit_value', 10, 2)->default(1)->after('is_base_unit')->comment('Base unit value');
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
            $table->dropColumn('base_unit_value');
        });
        Schema::table('sale_return_details', function (Blueprint $table) {
            $table->dropColumn('base_unit_value');
        });
        Schema::table('purchase_return_details', function (Blueprint $table) {
            $table->dropColumn('base_unit_value');
        });
        Schema::table('purchase_invoice_details', function (Blueprint $table) {
            $table->dropColumn('base_unit_value');
        });
    }
};

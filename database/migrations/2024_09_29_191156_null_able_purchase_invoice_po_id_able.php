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
        Schema::table('purchase_invoices', function(Blueprint $table){
            $table->dropForeign(['po_id']);
            
            // Modify the 'po_id' column to be nullable
            $table->foreignId('po_id')->nullable()->change();

            // Add the foreign key constraint back
            $table->foreign('po_id')->references('id')->on('purchase_orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['po_id']);

            // Revert the 'po_id' column to NOT nullable
            $table->foreignId('po_id')->nullable(false)->change();

            // Add the foreign key constraint back
            $table->foreign('po_id')->references('id')->on('purchase_orders');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tables = [
        'users',
        'sales',
        'purchase_orders',
        'purchase_invoices',
        'purchase_quotations',
        'purchase_requests',
        'products',
        'parties',
        'product_categories',
        'fields',
        'inventories',
        'user_roles',
        'configurations',
        'password_resets',
        'party_groups',
        'order_transactions',
        'product_arrtributes'
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        
        foreach ($this->tables as $key => $val) {
            if(!Schema::hasColumn($val,'store_id')){
                Schema::table($val, function (Blueprint $table) {
                    $table->integer('store_id')->nullable();
                });
            }
            
            if(!Schema::hasColumn($val,'user_id')){
                Schema::table($val, function (Blueprint $table) {
                    $table->integer('user_id')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $key => $val) {
            if(Schema::hasColumn($val,'store_id')){
                Schema::table($val, function (Blueprint $table) {
                    $table->dropColumn('store_id');
                });
            }
            
            if(Schema::hasColumn($val,'user_id')){
                Schema::table($val, function (Blueprint $table) {
                    $table->dropColumn('user_id');
                });
            }
        } 
    }
};

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
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('account_number')->nullable()->after('id'); // Add unique account number
            $table->unsignedBigInteger('parent_id')->nullable()->after('type'); // Add parent_id for hierarchical structure
            $table->foreign('parent_id')->references('id')->on('accounts')->onDelete('cascade'); // Foreign key to self (accounts)
            $table->decimal('current_balance', 15, 2)->default(0.00)->after('opening_balance'); // Add current_balance field

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('account_number');
            $table->dropForeign(['parent_id']); // Drop foreign key constraint
            $table->dropColumn('parent_id');
            $table->dropColumn('current_balance');
        });
    }
};

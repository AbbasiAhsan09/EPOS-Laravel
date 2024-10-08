<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            DB::statement("ALTER TABLE `accounts` MODIFY COLUMN `type` ENUM('assets', 'expenses', 'income', 'equity', 'liabilities', 'drawings')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `your_table_name` MODIFY COLUMN `type` ENUM('assets', 'expenses', 'income', 'equity', 'liabilities')");
    }
};

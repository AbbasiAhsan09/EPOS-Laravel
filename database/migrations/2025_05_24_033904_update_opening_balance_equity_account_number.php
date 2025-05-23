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
        DB::table('accounts')
            ->where('title', 'Opening Balance Equity')
            ->update(['account_number' => 3500]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('accounts')
            ->where('title', 'Opening Balance Equity')
            ->update(['account_number' => null]);
    }
};

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
        Schema::table('voucher_types', function (Blueprint $table) {
            $table->string("account_types")->nullable();
            $table->boolean("show_coa")->default(false);
            $table->boolean("show_head")->default(false);
            $table->string("account_reference_types")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voucher_types', function (Blueprint $table) {
            $table->dropColumn(['account_types','show_coa','show_head','account_reference_types']);
        });
    }
};

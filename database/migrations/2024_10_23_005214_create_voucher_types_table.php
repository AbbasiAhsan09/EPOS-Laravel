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
        Schema::create('voucher_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId("account_id")->constrained("accounts")->onDelete("cascade");
            $table->foreignId("store_id")->constrained("stores")->onDelete("cascade");
            $table->string("slug")->unique();
            $table->string("name");
            $table->enum("type",["receipt","payment","transfer"]);
            $table->string("description")->nullable();
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
        Schema::dropIfExists('voucher_types');
    }
};

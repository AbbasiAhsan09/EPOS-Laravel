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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string("doc_no");
            $table->foreignId("store_id")->constrained("stores")->onDelete("cascade");
            $table->foreignId("user_id")->nullable()->constrained("users")->onDelete("cascade");
            $table->date("date");
            $table->text("note")->nullable();
            $table->decimal("total",11,2)->default(0);
            $table->foreignId("voucher_type_id")->constrained("voucher_types");
            $table->enum("mode",["cash","cheque","credit_card","offset","online","pay_order"])->nullable();
            $table->string("reference_no")->nullable();
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
        Schema::dropIfExists('vouchers');
    }
};

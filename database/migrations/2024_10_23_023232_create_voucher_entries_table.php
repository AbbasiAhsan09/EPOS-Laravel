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
        Schema::create('voucher_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId("voucher_id")->constrained("vouchers")->onDelete("cascade");
            // $table->foreignId("account_id")->constrained("accounts")->onDelete("cascade");
            $table->string("reference")->nullable();
            $table->string("reference_type")->nullable();
            $table->integer("reference_id")->nullable();
            $table->foreignId("sale_id")->nullable()->constrained("sales")->onDelete("cascade");
            $table->foreignId("purchase_invoice_id")->nullable()->constrained("purchase_invoices")->onDelete("cascade");
            $table->decimal("amount",15,2)->nullable();
            // $table->enum("entry_type",["credit","debit"]);
            $table->text("description")->nullable();
            $table->foreignId("store_id")->constrained('stores');
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
        Schema::dropIfExists('voucher_entries');
    }
};

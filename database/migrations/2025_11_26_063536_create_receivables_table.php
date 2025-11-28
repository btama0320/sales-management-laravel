<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_receivables_table.php
public function up()
{
    Schema::create('receivables', function (Blueprint $table) {
        $table->id();
        $table->date('slip_date')->nullable();      // 伝票日付
        $table->string('slip_no')->unique();        // 伝票番号
        $table->string('shipper_code')->nullable(); // 荷主コード
        $table->string('shipper_name')->nullable(); // 荷主名
        $table->string('customer_code')->nullable();// 得意先コード
        $table->string('customer_name')->nullable();// 得意先名
        $table->string('department')->nullable();   // 担当部署
        $table->string('honorific')->nullable();    // 敬称
        $table->string('billing_code')->nullable(); // 請求先コード
        $table->string('billing_name')->nullable(); // 請求先名
        $table->string('item_code_header')->nullable(); // カテゴリコード
        $table->string('item_name_header')->nullable(); // カテゴリ名
        $table->string('carrier_code')->nullable(); // 運送会社コード
        $table->string('carrier_name')->nullable(); // 運送会社名
        $table->text('summary')->nullable();        // 摘要
        $table->date('sales_date')->nullable();     // 販売日
        $table->boolean('is_draft')->default(false);// 仮伝票フラグ
        $table->timestamps();
        $table->unsignedBigInteger('purchase_invoice_id')->nullable(); // 仕入伝票との紐付け
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};

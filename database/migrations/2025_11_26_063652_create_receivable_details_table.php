<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_xx_xx_create_receivable_details_table.php
public function up()
{
    Schema::create('receivable_details', function (Blueprint $table) {
        $table->id();
        $table->foreignId('receivable_id')->constrained()->onDelete('cascade'); // 外部キー
        $table->integer('row_no')->nullable();      // 行番号
        $table->string('item_code')->nullable();    // 商品コード
        $table->string('item_name')->nullable();    // 商品名
        $table->string('package')->nullable();      // 荷姿
        $table->string('unit')->nullable();         // 量目
        $table->string('grade')->nullable();        // 等級
        $table->string('class')->nullable();        // 階級
        $table->decimal('quantity', 10, 2)->default(0);   // 数量
        $table->decimal('unit_price', 10, 2)->default(0);// 単価
        $table->enum('tax_rate', ['8','10'])->default('10'); // 消費税率
        $table->decimal('amount', 12, 2)->default(0);     // 金額
        $table->text('remarks')->nullable();        // 備考
        $table->timestamps();
    });

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivable_details');
    }
};

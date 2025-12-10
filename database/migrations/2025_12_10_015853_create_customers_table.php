<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->string('code')->unique(); // 顧客コード
            $table->string('company_name');   // 会社名         
            $table->string('search_key_romaji')->nullable();
            $table->string('search_key_hiragana')->nullable();
            $table->string('search_key_katakana')->nullable();// 会社名検索用
            $table->string('address')->nullable(); // 住所
            $table->string('phone_number')->nullable(); // 電話番号
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

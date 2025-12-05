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
        Schema::table('item_types', function (Blueprint $table) {
            $table->string('search_key_romaji')->nullable()->after('name');
            $table->string('search_key_hiragana')->nullable()->after('search_key_romaji');
            $table->string('search_key_katakana')->nullable()->after('search_key_hiragana');
        });
    }

    public function down(): void
    {
        Schema::table('item_types', function (Blueprint $table) {
            $table->dropColumn(['search_key_romaji', 'search_key_hiragana', 'search_key_katakana']);
        });
    }
};

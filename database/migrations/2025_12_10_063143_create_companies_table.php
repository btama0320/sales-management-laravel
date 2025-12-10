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
        Schema::create('companies', function (Blueprint $table) {
        $table->id();
        $table->string('code', 20)->unique()->comment('会社コード');
        $table->string('name', 100)->comment('会社名');
        $table->string('address', 255)->nullable()->comment('住所');
        $table->string('tel', 20)->nullable()->comment('電話番号');
        $table->boolean('is_default')->default(false)->comment('自社フラグ');
        $table->timestamps();
    });

        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

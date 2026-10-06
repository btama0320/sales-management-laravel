<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('運送会社コード');
            $table->string('name', 100)->comment('運送会社名');
            $table->string('phone_number', 20)->nullable()->comment('電話番号');
            $table->string('address', 255)->nullable()->comment('住所');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};

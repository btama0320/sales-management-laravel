<?php

// database/migrations/2025_12_01_000000_create_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 20)->unique(); // 連番でもOK
            $table->foreignId('item_type_id')->constrained('item_types'); // 数字IDで紐付け
            $table->string('product_name', 255); // 鹿児島マル本、宮崎など
            $table->string('package', 100)->nullable();
            $table->string('unit', 100)->nullable();
            $table->string('grade', 100)->nullable();
            $table->string('class', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};

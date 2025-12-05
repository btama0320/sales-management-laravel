<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            // 胡瓜
            ['product_code' => 1, 'item_type_id' => 1, 'product_name' => '鹿児島さつま胡瓜', 'package' => 'DB', 'unit' => '5K', 'grade' => 'A', 'class' => 'L', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 2, 'item_type_id' => 1, 'product_name' => '宮崎ひなた胡瓜', 'package' => 'DB', 'unit' => '5K', 'grade' => 'B', 'class' => 'L', 'created_at' => now(), 'updated_at' => now()],

            // 大根
            ['product_code' => 3, 'item_type_id' => 2, 'product_name' => '鹿児島黒潮大根', 'package' => 'DB', 'unit' => '10K', 'grade' => 'AL', 'class' => '10本', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 4, 'item_type_id' => 2, 'product_name' => '北海道雪の華大根', 'package' => 'DB', 'unit' => '10K', 'grade' => 'AM', 'class' => '12本', 'created_at' => now(), 'updated_at' => now()],

            // 人参
            ['product_code' => 5, 'item_type_id' => 3, 'product_name' => '鹿児島さつま人参', 'package' => 'DB', 'unit' => '10K', 'grade' => 'A', 'class' => 'L', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 6, 'item_type_id' => 3, 'product_name' => '宮崎ひなた人参', 'package' => 'DB', 'unit' => '10K', 'grade' => 'B', 'class' => 'M', 'created_at' => now(), 'updated_at' => now()],

            // 玉葱
            ['product_code' => 7, 'item_type_id' => 4, 'product_name' => '北海道北見玉葱', 'package' => 'DB', 'unit' => '20K', 'grade' => 'A', 'class' => 'L', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 8, 'item_type_id' => 4, 'product_name' => '淡路島黄金玉葱', 'package' => 'ネット', 'unit' => '20K', 'grade' => 'A', 'class' => 'L', 'created_at' => now(), 'updated_at' => now()],

            // キャベツ
            ['product_code' => 9, 'item_type_id' => 5, 'product_name' => '鹿児島春風キャベツ', 'package' => 'DB', 'unit' => '10K', 'grade' => 'A', 'class' => '8玉', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 10, 'item_type_id' => 5, 'product_name' => '群馬高原キャベツ', 'package' => 'DB', 'unit' => '10K', 'grade' => 'A', 'class' => '8玉', 'created_at' => now(), 'updated_at' => now()],

            // レタス
            ['product_code' => 11, 'item_type_id' => 6, 'product_name' => '長野信州レタス', 'package' => 'DB', 'unit' => '10K', 'grade' => 'A', 'class' => '12玉', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 12, 'item_type_id' => 6, 'product_name' => '鹿児島さつまレタス', 'package' => 'DB', 'unit' => '10K', 'grade' => 'A', 'class' => '14玉', 'created_at' => now(), 'updated_at' => now()],

            // ゴーヤ
            ['product_code' => 13, 'item_type_id' => 7, 'product_name' => '沖縄島風ゴーヤ', 'package' => 'DB', 'unit' => '5K', 'grade' => 'A', 'class' => 'L', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 14, 'item_type_id' => 7, 'product_name' => '鹿児島さつまゴーヤ', 'package' => 'DB', 'unit' => '3K', 'grade' => 'A', 'class' => 'L', 'created_at' => now(), 'updated_at' => now()],

            // 小松菜
            ['product_code' => 15, 'item_type_id' => 8, 'product_name' => '東京江戸小松菜', 'package' => 'DB', 'unit' => '3.75K', 'grade' => 'A', 'class' => '150g×25束', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 16, 'item_type_id' => 8, 'product_name' => '鹿児島さつま小松菜', 'package' => 'DB', 'unit' => '3K', 'grade' => 'A', 'class' => '150g×20束', 'created_at' => now(), 'updated_at' => now()],

            // トマト
            ['product_code' => 17, 'item_type_id' => 9, 'product_name' => '宮崎ひなたトマト', 'package' => 'DB', 'unit' => '4K', 'grade' => 'AL', 'class' => '20玉', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 18, 'item_type_id' => 9, 'product_name' => '鹿児島さつまトマト', 'package' => 'DB', 'unit' => '4K', 'grade' => 'AM', 'class' => '24玉', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 19, 'item_type_id' => 9, 'product_name' => '北海道雪の華トマト', 'package' => 'DB', 'unit' => '4K', 'grade' => 'BL', 'class' => '20玉', 'created_at' => now(), 'updated_at' => now()],
            ['product_code' => 20, 'item_type_id' => 9, 'product_name' => '熊本火の国トマト', 'package' => 'DB', 'unit' => '4K', 'grade' => '秀2L', 'class' => '16玉', 'created_at' => now(), 'updated_at' => now()],
        ]);

    }
}

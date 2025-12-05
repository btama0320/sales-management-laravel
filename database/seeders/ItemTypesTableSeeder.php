<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('item_types')->insert([
            ['name' => '胡瓜'],
            ['name' => '大根'],
            ['name' => '人参'],
            ['name' => '玉葱'],
            ['name' => 'キャベツ'],
            ['name' => 'レタス'],
            ['name' => 'ゴーヤ'],
            ['name' => '小松菜'],
            ['name' => 'トマト'],
        ]);

        DB::table('item_types')
            ->where('name', '胡瓜')
            ->update([
                'search_key_romaji' => 'ki',
                'search_key_hiragana' => 'き',
                'search_key_katakana' => 'キ',
            ]);

        DB::table('item_types')
            ->where('name', '大根')
            ->update([
                'search_key_romaji' => 'da',
                'search_key_hiragana' => 'だ',
                'search_key_katakana' => 'ダ',
            ]);

        DB::table('item_types')
            ->where('name', '人参')
            ->update([
                'search_key_romaji' => 'ni',
                'search_key_hiragana' => 'に',
                'search_key_katakana' => 'ニ',
            ]);

        DB::table('item_types')
            ->where('name', '玉葱')
            ->update([
                'search_key_romaji' => 'ta',
                'search_key_hiragana' => 'た',
                'search_key_katakana' => 'タ',
            ]);

        DB::table('item_types')
            ->where('name', 'キャベツ')
            ->update([
                'search_key_romaji' => 'ki',
                'search_key_hiragana' => 'き',
                'search_key_katakana' => 'キ',
            ]);

        DB::table('item_types')
            ->where('name', 'レタス')
            ->update([
                'search_key_romaji' => 're',
                'search_key_hiragana' => 'れ',
                'search_key_katakana' => 'レ',
            ]);

        DB::table('item_types')
            ->where('name', 'ゴーヤ')
            ->update([
                'search_key_romaji' => 'go',
                'search_key_hiragana' => 'ご',
                'search_key_katakana' => 'ゴ',
            ]);

        DB::table('item_types')
            ->where('name', '小松菜')
            ->update([
                'search_key_romaji' => 'ko',
                'search_key_hiragana' => 'こ',
                'search_key_katakana' => 'コ',
            ]);

        DB::table('item_types')
            ->where('name', 'トマト')
            ->update([
                'search_key_romaji' => 'to',
                'search_key_hiragana' => 'と',
                'search_key_katakana' => 'ト',
            ]);

    }
}

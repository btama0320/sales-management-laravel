<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customers')->insert([
            [
                'code' => '1',
                'company_name' => '株式会社さくら商事',
                'address' => '鹿児島市中央町1-1',
                'phone_number' => '099-111-1111',
                'search_key_romaji' => 'sakura',
                'search_key_hiragana' => 'さくら',
                'search_key_katakana' => 'サクラ',
            ],
            [
                'code' => '2',
                'company_name' => '有限会社グリーン物流',
                'address' => '鹿児島市武町2-2',
                'phone_number' => '099-222-2222',
                'search_key_romaji' => 'green',
                'search_key_hiragana' => 'ぐりーん',
                'search_key_katakana' => 'グリーン',
            ],
            [
                'code' => '3',
                'company_name' => '南九州運送株式会社',
                'address' => '鹿児島市鴨池3-3',
                'phone_number' => '099-333-3333',
                'search_key_romaji' => 'minamikyushu',
                'search_key_hiragana' => 'みなみきゅうしゅう',
                'search_key_katakana' => 'ミナミキュウシュウ',
            ],
            [
                'code' => '4',
                'company_name' => '株式会社フェニックス食品',
                'address' => '鹿児島市紫原4-4',
                'phone_number' => '099-444-4444',
                'search_key_romaji' => 'phoenix',
                'search_key_hiragana' => 'ふぇにっくす',
                'search_key_katakana' => 'フェニックス',
            ],
            [
                'code' => '5',
                'company_name' => '有限会社ブルーライン',
                'address' => '鹿児島市谷山5-5',
                'phone_number' => '099-555-5555',
                'search_key_romaji' => 'blue',
                'search_key_hiragana' => 'ぶるー',
                'search_key_katakana' => 'ブルー',
            ],
            [
                'code' => '6',
                'company_name' => '株式会社九州電材',
                'address' => '鹿児島市宇宿6-6',
                'phone_number' => '099-666-6666',
                'search_key_romaji' => 'densai',
                'search_key_hiragana' => 'でんさい',
                'search_key_katakana' => 'デンサイ',
            ],
            [
                'code' => '7',
                'company_name' => '有限会社ハヤブサ運輸',
                'address' => '鹿児島市郡元7-7',
                'phone_number' => '099-777-7777',
                'search_key_romaji' => 'hayabusa',
                'search_key_hiragana' => 'はやぶさ',
                'search_key_katakana' => 'ハヤブサ',
            ],
            [
                'code' => '8',
                'company_name' => '株式会社オレンジ商会',
                'address' => '鹿児島市荒田8-8',
                'phone_number' => '099-888-8888',
                'search_key_romaji' => 'orange',
                'search_key_hiragana' => 'おれんじ',
                'search_key_katakana' => 'オレンジ',
            ],
            [
                'code' => '9',
                'company_name' => '有限会社ホワイト物流',
                'address' => '鹿児島市下荒田9-9',
                'phone_number' => '099-999-9999',
                'search_key_romaji' => 'white',
                'search_key_hiragana' => 'ほわいと',
                'search_key_katakana' => 'ホワイト',
            ],
            [
                'code' => '10',
                'company_name' => '株式会社クローバー食品',
                'address' => '鹿児島市上荒田10-10',
                'phone_number' => '099-101-0101',
                'search_key_romaji' => 'clover',
                'search_key_hiragana' => 'くろーばー',
                'search_key_katakana' => 'クローバー',
            ],
        ]);
    }
}
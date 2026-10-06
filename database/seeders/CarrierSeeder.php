<?php

namespace Database\Seeders;

use App\Models\Carrier;
use Illuminate\Database\Seeder;

class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        $carriers = [
            [
                'code' => 'C001',
                'name' => '南九州運送株式会社',
                'phone_number' => '099-201-1001',
                'address' => '鹿児島県鹿児島市谷山港1-1-1',
                'search_key_romaji' => 'minamikyushu',
                'search_key_hiragana' => 'みなみきゅうしゅう',
                'search_key_katakana' => 'ミナミキュウシュウ',
            ],
            [
                'code' => 'C002',
                'name' => 'さくら物流有限会社',
                'phone_number' => '099-201-1002',
                'address' => '鹿児島県鹿児島市南栄2-2-2',
                'search_key_romaji' => 'sakura',
                'search_key_hiragana' => 'さくら',
                'search_key_katakana' => 'サクラ',
            ],
            [
                'code' => 'C003',
                'name' => '九州フレッシュ便株式会社',
                'phone_number' => '099-201-1003',
                'address' => '鹿児島県鹿児島市東開町3-3-3',
                'search_key_romaji' => 'kyushufreshbin',
                'search_key_hiragana' => 'きゅうしゅうふれっしゅびん',
                'search_key_katakana' => 'キュウシュウフレッシュビン',
            ],
            [
                'code' => 'C004',
                'name' => 'みなみ輸送株式会社',
                'phone_number' => '099-201-1004',
                'address' => '鹿児島県鹿児島市卸本町4-4-4',
                'search_key_romaji' => 'minamiyuso',
                'search_key_hiragana' => 'みなみゆそう',
                'search_key_katakana' => 'ミナミユソウ',
            ],
            [
                'code' => 'C005',
                'name' => 'グリーンライン運輸株式会社',
                'phone_number' => '099-201-1005',
                'address' => '鹿児島県鹿児島市七ツ島5-5-5',
                'search_key_romaji' => 'greenlineunyu',
                'search_key_hiragana' => 'ぐりーんらいんうんゆ',
                'search_key_katakana' => 'グリーンラインウンユ',
            ],
        ];

        foreach ($carriers as $carrier) {
            Carrier::updateOrCreate(
                ['code' => $carrier['code']],
                $carrier,
            );
        }
    }
}

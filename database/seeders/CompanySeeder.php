<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Company::create([
            'code' => '0001',
            'name' => '自社株式会社',
            'address' => '鹿児島市中央町1-2-3',
            'tel' => '099-123-4567',
            'is_default' => true,
        ]);
    }
}

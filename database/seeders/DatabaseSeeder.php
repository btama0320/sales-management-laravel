<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        // // テスト用のダミーユーザー（業務用仕様に合わせる）
        // \App\Models\User::factory()->create([
        //     'user_id' => 'TEST001',
        //     'password' => bcrypt('testpass'),
        //     'must_change_password' => false,
        // ]);
    }

}

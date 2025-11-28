<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 管理者用アカウント（adminを先頭に）
        User::create([
            'user_id' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('admin1234'), // 管理者用の初期PW
            'must_change_password' => false,       // 管理者は変更不要でもOK
        ]);

        // 社員用アカウント（5件）
        User::create([
            'user_id' => 'A001',
            'role' => 'user',
            'password' => Hash::make('init1234'),
            'must_change_password' => true,
        ]);

        User::create([
            'user_id' => 'A002',
            'role' => 'user',
            'password' => Hash::make('init5678'),
            'must_change_password' => true,
        ]);

        User::create([
            'user_id' => 'A003',
            'role' => 'user',
            'password' => Hash::make('init9012'),
            'must_change_password' => true,
        ]);

        User::create([
            'user_id' => 'A004',
            'role' => 'user',
            'password' => Hash::make('init3456'),
            'must_change_password' => true,
        ]);

        User::create([
            'user_id' => 'A005',
            'role' => 'user',
            'password' => Hash::make('init7890'),
            'must_change_password' => true,
        ]);

    }
}

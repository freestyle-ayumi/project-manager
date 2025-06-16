<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // User モデルを使用するために追記
use Illuminate\Support\Facades\Hash; // パスワードハッシュのために追記

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // ログインパスワードは 'password'
        ]);

        // 必要であれば、さらにユーザーを追加
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('secret'), // パスワードは 'secret'
        ]);
    }
}
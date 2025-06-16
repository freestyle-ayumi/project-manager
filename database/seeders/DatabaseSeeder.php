<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class, // RoleがUserより先に必要
            ProjectStatusSeeder::class, // Projectより先に必要
            ExpenseStatusSeeder::class, // Expenseより先に必要
            UserSeeder::class, // Roleの後にUser
            ClientSeeder::class,
        ]);
        // 必要に応じて他のシーダーを追加
    }
}
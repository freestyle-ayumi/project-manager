<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExpenseStatus;

class ExpenseStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => '申請中', 'description' => '経費申請が提出された状態'],
            ['name' => '承認済み', 'description' => '経費が承認された状態'],
            ['name' => '却下', 'description' => '経費が却下された状態'],
            ['name' => '支払い済み', 'description' => '経費が支払い済みである状態'],
        ];

        foreach ($statuses as $status) {
            ExpenseStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}
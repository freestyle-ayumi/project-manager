<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client; // Client モデルを使用するために追記

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'name' => '株式会社ABC商事',
            'email' => 'abc@example.com',
            'phone' => '03-1234-5678',
            'address' => '東京都港区ABCビル1-1-1',
            'notes' => '主要顧客',
        ]);

        Client::create([
            'name' => '合同会社XYZテック',
            'email' => 'xyz@example.com',
            'phone' => '06-9876-5432',
            'address' => '大阪府大阪市XYZビル2-2-2',
            'notes' => '新規開拓先',
        ]);

        // 必要に応じてさらに追加
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProjectStatus; // モデルを使用するために追記

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => '新規', 'description' => 'プロジェクトの初期段階', 'color_code' => '#6c757d'], // ★color_codeを追加★
            ['name' => '進行中', 'description' => 'プロジェクトが現在進行している状態', 'color_code' => '#007bff'], // 例
            ['name' => '一時停止', 'description' => 'プロジェクトが一時的に中断されている状態', 'color_code' => '#ffc107'], // 例
            ['name' => '完了', 'description' => 'プロジェクトが完全に終了した状態', 'color_code' => '#28a745'], // 例
            ['name' => 'キャンセル', 'description' => 'プロジェクトがキャンセルされた状態', 'color_code' => '#dc3545'], // 例
        ];

        foreach ($statuses as $status) {
            ProjectStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}
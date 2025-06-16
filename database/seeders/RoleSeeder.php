<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'master', 'description' => 'システム全体の管理者。最高権限を持つ。'],
            ['id' => 2, 'name' => 'main', 'description' => '主要な業務担当者であり、マネージャー権限を兼ねる。'],
            ['id' => 3, 'name' => 'part_timer', 'description' => '限定された日常業務を実行するスタッフ。'],
        ]);
    }
}
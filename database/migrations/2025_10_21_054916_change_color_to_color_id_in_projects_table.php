<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // 新しい color_id カラムを追加
            $table->unsignedBigInteger('color_id')->nullable()->after('color');
        });

        // 既存の color HEXコード を color_id に変換
        $colorMap = DB::table('colors')->pluck('id', 'hex_code'); // ['#3B82F6' => 1, ...]

        $projects = DB::table('projects')->get();
        foreach ($projects as $project) {
            if (isset($colorMap[$project->color])) {
                DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['color_id' => $colorMap[$project->color]]);
            }
        }

        Schema::table('projects', function (Blueprint $table) {
            // 旧 color カラムを削除
            $table->dropColumn('color');
            // color_id を NOT NULL & 外部キー制約
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // 外部キーを削除
            $table->dropForeign(['color_id']);
            // color カラムを戻す
            $table->string('color', 7)->nullable();
            // color_id カラムを削除
            $table->dropColumn('color_id');
        });
    }
};

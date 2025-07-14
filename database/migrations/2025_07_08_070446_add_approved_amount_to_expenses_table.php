<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // approved_amount カラムを追加。nullableでdecimal型、デフォルト値は0
            // 必要に応じて桁数と精度を調整してください (例: decimal(10, 2))
            $table->decimal('approved_amount', 15, 2)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // ロールバック時に approved_amount カラムを削除
            $table->dropColumn('approved_amount');
        });
    }
};

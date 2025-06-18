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
        Schema::table('quotes', function (Blueprint $table) {
            // expiry_date カラムを DATE 型から STRING 型に変更
            $table->string('expiry_date')->change(); // ★この行を追加/変更★
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            // ロールバック時に DATE 型に戻す（ただし、文字列が格納されているとエラーになる可能性あり）
            // このメソッドは主に開発中でロールバックが必要な場合のため。
            // 本番環境で実行する際はデータ損失に注意。
            $table->date('expiry_date')->change(); // ★この行を追加/変更★
        });
    }
};
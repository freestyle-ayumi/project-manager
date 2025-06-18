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
            // subject カラムを追加
            $table->string('subject')->after('expiry_date')->comment('件名'); // ★この行を追加★

            // expiry_date カラムを string 型に変更 (念のため)
            // 既に string に変更するマイグレーションがある場合は、この行は削除してもOK
            // なければ追加してください
            $table->string('expiry_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            // subject カラムを削除
            $table->dropColumn('subject');

            // expiry_date カラムを date 型に戻す (必要であれば)
            // このメソッドは主に開発中でロールバックが必要な場合のため。
            // $table->date('expiry_date')->change();
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // projects テーブル作成
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); // 顧客ID (任意)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // プロジェクト作成ユーザーID (必須)
            $table->foreignId('project_status_id')->nullable()->constrained('project_statuses')->onDelete('set null'); // プロジェクトステータスID (任意)
            $table->string('name')->comment('プロジェクト名');
            $table->string('venue')->comment('催事場所'); // ← 追加
            $table->text('description')->nullable()->comment('プロジェクト概要');
            $table->date('start_date')->nullable()->comment('開始日');
            $table->date('end_date')->nullable()->comment('終了日');
            $table->timestamps();
        });

        // quotes テーブルの project_id にユニーク制約追加
        Schema::table('quotes', function (Blueprint $table) {
            $table->unique('project_id');
        });
    }

    public function down(): void
    {
        // quotes テーブルのユニーク制約削除
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropUnique(['project_id']);
        });

        // projects テーブル削除
        Schema::dropIfExists('projects');
    }
};

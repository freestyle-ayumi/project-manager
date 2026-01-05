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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('user_id')->comment('登録者');
            $table->string('name')->comment('プロジェクト名');
            $table->text('description')->nullable()->comment('プロジェクト概要');
            $table->date('start_date')->nullable()->comment('開始日');
            $table->date('end_date')->nullable()->comment('終了日');
            $table->string('venue')->nullable()->comment('催事場所');
            $table->tinyInteger('color')->unsigned()->default(1)->comment('=colors.id');
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

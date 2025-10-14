<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('name')->comment('タスク名');
            $table->text('description')->nullable()->comment('タスク詳細');
            $table->date('start_date')->nullable()->comment('開始日');
            $table->date('due_date')->nullable()->comment('期日');
            $table->string('status')->default('未完了')->comment('ステータス（未完了 / 完了など）');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('登録者ID');
            $table->string('priority', 10)->nullable()->comment('優先度（高/中/低）');
            $table->timestamps();
        });

        // 担当者用の中間テーブル
        Schema::create('task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_user');
        Schema::dropIfExists('tasks');
    }
};

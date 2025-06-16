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
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade'); // 関連するプロジェクトID
            $table->string('name')->comment('タスク名');
            $table->text('description')->nullable()->comment('タスク詳細');
            $table->date('due_date')->nullable()->comment('期日');
            $table->string('status')->default('未完了')->comment('ステータス（例：未完了、完了）');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // 担当ユーザーID (任意)
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
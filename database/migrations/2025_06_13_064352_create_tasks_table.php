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
            $table->date('start_date')->nullable()->comment('依頼日');
            $table->time('start_time')->nullable()->comment('開始時間');
            $table->date('plans_date')->nullable()->comment('完了希望日');
            $table->date('due_date')->nullable()->comment('期日');
            $table->string('status')->default('未完了')->comment('ステータス（例：未完了、完了）');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('登録者ID');
            $table->string('priority', 10)->nullable()->comment('優先度（高/中/低）');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_user');
        Schema::dropIfExists('tasks');
    }
};

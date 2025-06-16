<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null'); // 顧客ID (任意)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // プロジェクト作成ユーザーID (必須)
            $table->foreignId('project_status_id')->nullable()->constrained('project_statuses')->onDelete('set null'); // プロジェクトステータスID (任意、null許容)
            $table->string('name')->comment('プロジェクト名');
            $table->text('description')->nullable()->comment('プロジェクト概要');
            $table->date('start_date')->nullable()->comment('開始日');
            $table->date('end_date')->nullable()->comment('終了日');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
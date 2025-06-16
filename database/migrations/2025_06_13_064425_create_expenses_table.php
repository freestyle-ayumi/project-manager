<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('expense_status_id')->nullable()->constrained('expense_statuses')->onDelete('set null'); // 経費ステータスID
            $table->date('date')->comment('日付');
            $table->string('category')->comment('カテゴリ（例：交通費、消耗品費）');
            $table->text('description')->nullable()->comment('経費詳細');
            $table->decimal('amount', 10, 2)->comment('金額');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
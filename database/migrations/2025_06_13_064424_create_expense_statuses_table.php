<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('経費ステータス名（例：申請中、承認済み、却下）');
            $table->text('description')->nullable()->comment('ステータスの説明');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('expense_statuses');
    }
};
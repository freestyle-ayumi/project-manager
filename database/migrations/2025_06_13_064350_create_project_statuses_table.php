<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('プロジェクトステータス名（例：新規、進行中、完了）');
            $table->string('color_code', 7)->nullable()->comment('ステータス表示色（例：#RRGGBB）');
            $table->text('description')->nullable()->comment('ステータスの説明');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('project_statuses');
    }
};
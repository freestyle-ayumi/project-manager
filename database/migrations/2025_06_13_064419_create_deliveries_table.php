<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('delivery_date')->comment('納品日');
            $table->string('description')->nullable()->comment('納品物の説明');
            $table->string('file_path')->nullable()->comment('納品物ファイルのパス');
            $table->string('status')->default('納品済み')->comment('ステータス（例：納品済み, 受領待ち）');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
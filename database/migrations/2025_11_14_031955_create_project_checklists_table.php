<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_checklists', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->cascadeOnDelete();
            $table->string('name')->comment('提出物名');
            $table->string('link')->nullable()->comment('提出物リンクURL');
            $table->enum('status', ['未','作','済'])->default('未')->comment('ステータス');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_checklists');
    }
};

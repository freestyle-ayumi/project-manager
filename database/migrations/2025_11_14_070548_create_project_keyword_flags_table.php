<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_keyword_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->cascadeOnDelete();
            $table->string('keyword')->comment('含まれるか判定する文字列');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_keyword_flags');
    }
};

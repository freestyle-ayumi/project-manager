<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('user_id')->comment('登録者');
            $table->string('name')->comment('プロジェクト名');
            $table->text('description')->nullable()->comment('プロジェクト概要');
            $table->date('start_date')->nullable()->comment('開始日');
            $table->date('end_date')->nullable()->comment('終了日');
            $table->string('venue')->nullable()->comment('催事場所');

            // ★ 最初から color_id
            $table->foreignId('color_id')
                ->constrained('colors')
                ->restrictOnDelete()
                ->comment('colors.id');

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

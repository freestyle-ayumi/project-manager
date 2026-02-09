<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('start_date');  // 出張開始日
            $table->date('end_date')->nullable();  // 出張終了日（終了ボタンを押すまでnull）
            $table->text('note');  // メモ（必須）
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_trips');
    }
};
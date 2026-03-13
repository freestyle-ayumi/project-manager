<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->cascadeOnDelete();

            $table->enum('type', [
                'check_in',
                'check_out',
                'break_start',
                'break_end',
                'business_trip_start',
                'business_trip_end',
                'break_30',
                'break_60'
            ]);

            $table->dateTime('timestamp');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->integer('distance')->nullable();
            $table->boolean('is_valid');
            $table->boolean('is_business_trip')->default(false);
            $table->text('note')->nullable();

            // 計算用カラムを追加
            $table->integer('work_minutes')->nullable()->comment('実働合計分');
            $table->integer('night_minutes')->nullable()->comment('深夜労働分');
            $table->integer('overtime_minutes')->nullable()->comment('残業分');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};

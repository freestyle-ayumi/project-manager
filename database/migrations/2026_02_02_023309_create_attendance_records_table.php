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
            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnDelete();

            $table->enum('type', [
                'check_in',
                'check_out',
                'break_start',
                'break_end',
                'business_trip_start',
                'business_trip_end',
            ]);

            $table->dateTime('timestamp');

            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();

            $table->integer('distance')->nullable();

            $table->boolean('is_valid');
            $table->boolean('is_business_trip')->default(false)->after('is_valid');
            $table->text('note')->nullable();
            if (!Schema::hasColumn('attendance_records', 'work_minutes')) {
                $table->integer('work_minutes')->nullable()->after('note');
            }
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};

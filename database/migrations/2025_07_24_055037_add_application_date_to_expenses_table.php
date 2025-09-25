<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // application_date カラムを追加
            // existing_column の後に配置するか、任意の位置に配置
            $table->date('application_date')->after('expense_status_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // ロールバック時にカラムを削除
            $table->dropColumn('application_date');
        });
    }
};


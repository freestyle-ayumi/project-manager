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
        Schema::table('quotes', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('expiry_date'); // 納品予定日
            $table->string('delivery_location')->nullable()->after('delivery_date'); // 納品場所
            $table->string('payment_terms')->nullable()->after('delivery_location'); // お支払条件
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['delivery_date', 'delivery_location', 'payment_terms']);
        });
    }
};

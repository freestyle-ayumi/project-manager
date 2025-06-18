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
        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade'); // 関連する見積書ID
            $table->string('item_name')->comment('項目名'); // ★追加
            $table->decimal('price', 10, 2)->comment('単価'); // ★追加
            $table->integer('quantity')->comment('数量');
            $table->string('unit', 50)->nullable()->comment('単位'); // ★追加
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('税率'); // ★追加
            $table->decimal('subtotal', 10, 2)->comment('小計'); // ★追加
            $table->decimal('tax', 10, 2)->comment('税額'); // ★追加
            $table->text('memo')->nullable()->comment('備考'); // ★追加
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
    }
};
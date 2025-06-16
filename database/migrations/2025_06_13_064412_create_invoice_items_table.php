<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade'); // 関連する請求書ID
            $table->string('description')->comment('項目説明');
            $table->integer('quantity')->comment('数量');
            $table->decimal('unit_price', 10, 2)->comment('単価');
            $table->decimal('amount', 10, 2)->comment('金額'); // 数量 * 単価
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
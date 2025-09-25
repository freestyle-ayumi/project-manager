<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained('expenses')->onDelete('cascade')->comment('申請ID');
            $table->string('description')->comment('項目説明');
            $table->decimal('amount', 10, 2)->comment('金額');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};
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
        $table->foreignId('expense_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->decimal('unit_price', 10, 2);
        $table->integer('quantity')->default(1);
        $table->string('unit')->nullable();
        $table->decimal('tax_rate', 5, 2)->default(0);
        $table->decimal('subtotal', 12, 2)->default(0);
        $table->decimal('tax', 12, 2)->default(0);

        $table->timestamps();
});
    }
    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};
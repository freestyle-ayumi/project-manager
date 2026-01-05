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
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->string('item_name');
            $table->decimal('price', 10, 0);
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit', 15, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('subtotal', 10, 0);
            $table->decimal('tax', 10, 0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
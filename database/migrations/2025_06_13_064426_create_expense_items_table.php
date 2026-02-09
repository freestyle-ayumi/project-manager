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

            $table->foreignId('expense_id')
                  ->constrained('expenses')
                  ->comment('expensesのid');

            // dump.sql に合わせる
            $table->string('item_name')
                  ->nullable()
                  ->comment('品名');

            $table->decimal('price', 12, 0)
                  ->default(0)
                  ->comment('単価');

            $table->integer('quantity')
                  ->default(1)
                  ->comment('数');

            $table->string('unit', 50)
                  ->nullable()
                  ->comment('単位');

            $table->decimal('tax_rate', 5, 0)
                  ->default(0)
                  ->comment('税率');

            $table->decimal('subtotal', 12, 0)
                  ->default(0)
                  ->comment('小計');

            $table->decimal('tax', 12, 0)
                  ->default(0)
                  ->comment('税');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};

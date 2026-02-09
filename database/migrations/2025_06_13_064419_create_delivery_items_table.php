<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('delivery_id')
                  ->constrained('deliveries');

            $table->string('item_name')->comment('項目名');

            $table->integer('price')
                  ->default(0)
                  ->comment('単価');

            $table->integer('quantity')
                  ->default(1)
                  ->comment('数量');

            $table->string('unit')
                  ->nullable()
                  ->comment('単位');

            $table->integer('tax_rate')
                  ->default(10)
                  ->comment('税率');

            $table->decimal('subtotal', 10, 0)
                  ->nullable()
                  ->comment('小計');

            $table->decimal('tax', 10, 0)
                  ->nullable()
                  ->comment('税額');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('delivery_number')->unique()->comment('納品書番号');
            $table->date('issue_date')->comment('発行日');
            $table->string('expiry_date')->comment('有効期限');
            $table->date('delivery_date')->nullable()->comment('納品日');
            $table->string('delivery_location')->nullable()->comment('納品場所');
            $table->string('payment_terms')->nullable()->comment('支払方法');
            $table->string('subject')->comment('件名');
            $table->decimal('total_amount', 10, 0)->default(0)->comment('合計金額');
            $table->string('pdf_path')->nullable()->comment('pdfパス');
            $table->string('status')->default('送信済み')->comment('ステータス（例：送信済み, 承認済み, 拒否済み）');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();
        });

    }
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
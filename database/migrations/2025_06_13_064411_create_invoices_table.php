<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_number')->unique()->comment('請求書番号');
            $table->date('issue_date')->comment('発行日');
            $table->date('due_date')->nullable()->comment('支払期日');
            $table->decimal('total_amount', 10, 2)->default(0.00)->comment('合計金額');
            $table->string('status')->default('未払い')->comment('ステータス（例：未払い, 支払い済み, 期限超過）');
            $table->text('notes')->nullable()->comment('備考');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
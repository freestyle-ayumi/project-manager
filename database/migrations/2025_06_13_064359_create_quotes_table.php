<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                  ->nullable()
                  ->constrained('projects')
                  ->onDelete('set null')
                  ->unique();

            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('quote_number')->unique()->comment('見積書番号');
            $table->date('issue_date')->comment('発行日');

            // expiry_date は string 型に変更
            $table->string('expiry_date')->nullable()->comment('有効期限');

            // 新規追加
            $table->string('subject')->nullable()->comment('件名');

            $table->date('delivery_date')->nullable()->comment('納品予定日');
            $table->string('delivery_location')->nullable()->comment('納品場所');
            $table->string('payment_terms')->nullable()->comment('お支払条件');

            $table->decimal('total_amount', 10, 2)->default(0.00)->comment('合計金額');
            $table->string('status')->default('送信済み')->comment('ステータス（例：送信済み, 承認済み, 拒否済み）');
            $table->text('notes')->nullable()->comment('備考');

            $table->string('pdf_path')->nullable()->comment('PDFファイルのURL');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};

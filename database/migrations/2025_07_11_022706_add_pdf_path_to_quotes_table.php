    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * マイグレーションを実行する
         */
        public function up(): void
        {
            Schema::table('quotes', function (Blueprint $table) {
                // PDFファイルのURLを保存するカラムを追加（nullableで既存データに対応）
                $table->string('pdf_path')->nullable()->after('total_amount');
            });
        }

        /**
         * マイグレーションを元に戻す
         */
        public function down(): void
        {
            Schema::table('quotes', function (Blueprint $table) {
                // ロールバック時にカラムを削除
                $table->dropColumn('pdf_path');
            });
        }
    };
    
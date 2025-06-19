    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            // quote_items テーブルに memo カラムが存在する場合にのみ削除
            if (Schema::hasColumn('quote_items', 'memo')) {
                Schema::table('quote_items', function (Blueprint $table) {
                    $table->dropColumn('memo');
                });
            }
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            // ロールバック時に memo カラムを再度追加（nullableでstring型）
            Schema::table('quote_items', function (Blueprint $table) {
                $table->string('memo')->nullable();
            });
        }
    };
    
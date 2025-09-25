<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('approver_id')->nullable()->comment('承認者ID')->after('amount');
            $table->text('remand_reason')->nullable()->comment('差し戻し理由')->after('approver_id');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('approver_id');
            $table->dropColumn('remand_reason');
        });
    }
};

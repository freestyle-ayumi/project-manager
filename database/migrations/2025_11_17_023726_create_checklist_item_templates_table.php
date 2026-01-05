<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('checklist_item_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_keyword_flag_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('提出物テンプレート名');
            $table->timestamps();

            $table->foreign('project_keyword_flag_id')
                ->references('id')
                ->on('project_keyword_flags')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_item_templates');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

        public function up()
        {
            Schema::table('quotes', function (Blueprint $table) {
                $table->unique('project_id');
            });
        }

        public function down()
        {
            Schema::table('quotes', function (Blueprint $table) {
                $table->dropUnique(['project_id']);
            });
        }

};

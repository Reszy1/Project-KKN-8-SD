<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Tambahkan kolom is_quiz, default 0 (artinya bukan kuis/praktik biasa)
            $table->boolean('is_quiz')->default(0)->after('activity_type');
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('is_quiz');
        });
    }
};

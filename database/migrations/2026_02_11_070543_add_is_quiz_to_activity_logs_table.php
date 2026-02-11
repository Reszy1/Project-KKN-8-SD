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
            // Menambahkan kolom is_quiz (default 0/false)
            $table->boolean('is_quiz')->default(0)->after('proof_image');
            
            // JAGA-JAGA: Pastikan kolom proof_image boleh kosong (nullable)
            // Kita ubah kolom yang sudah ada agar menerima nilai NULL
            $table->string('proof_image')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('is_quiz');
        });
    }
};

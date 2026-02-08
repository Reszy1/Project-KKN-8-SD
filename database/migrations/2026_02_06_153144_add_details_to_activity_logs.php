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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Menambahkan kolom foto bukti jika belum ada
            if (!Schema::hasColumn('activity_logs', 'proof_image')) {
                $table->string('proof_image')->nullable()->after('duration_seconds');
            }
            
            // Menambahkan kolom tanggal aktivitas (Penyebab Error Anda)
            if (!Schema::hasColumn('activity_logs', 'activity_date')) {
                $table->date('activity_date')->nullable()->after('proof_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::table('students', function (Blueprint $table) {
            // Cek jika kolom belum ada agar tidak error saat migrate ulang
            if (!Schema::hasColumn('students', 'password')) {
                $table->string('password')->nullable()->after('age'); // Password untuk login siswa (opsional)
            }
            if (!Schema::hasColumn('students', 'gender')) {
                $table->enum('gender', ['L', 'P'])->nullable()->after('name'); // L = Laki-laki, P = Perempuan
            }
            // Pastikan kolom class konsisten (bisa string nama kelas atau ID)
            // Disini kita pakai string nama kelas agar simpel sesuai input guru
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            //
        });
    }
};

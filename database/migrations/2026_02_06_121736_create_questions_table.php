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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->json('options'); // Menyimpan pilihan jawaban dalam bentuk array JSON
            $table->integer('answer_index'); // Index jawaban yang benar (0, 1, atau 2)
            $table->string('category'); // Contoh: 'brushing' atau 'handwashing'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};

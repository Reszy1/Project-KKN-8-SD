<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    // PASTIKAN SEMUA KOLOM INI ADA DI SINI
    protected $fillable = [
        'student_id',
        'activity_type',
        'duration_seconds',
        'proof_image',   // <--- Tambahan baru
        'activity_date'  // <--- Tambahan baru (PENTING UNTUK KALENDER)
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
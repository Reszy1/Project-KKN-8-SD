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
        'activity_date',
        'duration_seconds',
        'proof_image',
        'is_quiz', // <--- PASTIIN INI ADA
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
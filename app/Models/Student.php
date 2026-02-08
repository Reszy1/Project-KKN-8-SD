<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'name', 'gender', 'class', 'absent_no', 'age', 'total_points', 'password'
    ];

    // Relasi ke User (Orang Tua)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Log Aktivitas (Sikat gigi/Cuci tangan)
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Relasi ke Lencana (Badges)
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class);
    }
}

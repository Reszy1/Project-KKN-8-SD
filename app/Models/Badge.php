<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = ['name', 'description', 'image_path', 'required_points'];

    // Mengetahui siapa saja siswa yang sudah punya lencana ini
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }
}

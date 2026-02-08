<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    // Tambahkan properti ini agar kolom 'name' bisa diisi
    protected $fillable = ['name'];
}
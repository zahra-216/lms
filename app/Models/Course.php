<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'faculty_id',
        'description',
        'image'
    ];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}
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
        'image',
        'status',
    ];

    /** Courses the campus offers now (status "active"); the others stay in the data but are not offered. */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function levels()
    {
        return $this->hasMany(Level::class);
    }
}
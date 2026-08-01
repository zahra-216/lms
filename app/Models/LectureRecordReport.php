<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LectureRecordReport extends Model
{
    protected $fillable = ['lecturer_id', 'month', 'file_path', 'generated_at'];

    protected $casts = [
        'month' => 'date',
        'generated_at' => 'datetime',
    ];

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }
}
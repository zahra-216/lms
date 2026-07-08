<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'content',
        'file_path',
        'file_name',
        'file_size',
        'grade',
        'feedback',
        'is_late',
        'submitted_at',
        'graded_at',
        'graded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'grade' => 'decimal:2',
        'is_late' => 'boolean',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}



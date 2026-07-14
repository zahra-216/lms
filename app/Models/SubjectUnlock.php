<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectUnlock extends Model
{
    protected $fillable = ['student_id', 'subject_id', 'unlocked_at'];
}
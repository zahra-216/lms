<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateLog extends Model
{
    protected $fillable = ['user_type', 'user_id', 'scan_type', 'scanned_at', 'gate_device_id'];

    public function user()
    {
        return $this->user_type === 'student'
            ? Student::find($this->user_id)
            : Lecturer::find($this->user_id);
    }
}
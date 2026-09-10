<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateDevice extends Model
{
    protected $fillable = ['name', 'device_key', 'is_active', 'last_used_at'];

    public static function generateKey(): string
    {
        return bin2hex(random_bytes(32)); // random 64-character key
    }

    protected $casts = [
        'last_used_at' => 'datetime',
    ];
}
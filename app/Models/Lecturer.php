<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Lecturer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'qr_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function ensureQrToken()
    {
        if (!$this->qr_token) {
            $this->update(['qr_token' => \App\Models\GateDevice::generateKey()]);
        }
        return $this->qr_token;
    }
}

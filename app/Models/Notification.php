<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    // 🔥 Helper methods for UI

    public function getTitle()
    {
        return $this->data['title'] ?? '';
    }

    public function getMessage()
    {
        return $this->data['message'] ?? '';
    }

    public function getLink()
    {
        return $this->data['link'] ?? '#';
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }
}
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TimetableAssigned extends Notification
{
    use Queueable;

    protected $subject;
    protected $action; // 'assigned' or 'updated'

    public function __construct($subject, string $action = 'assigned')
    {
        $this->subject = $subject;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->action === 'updated' ? 'Timetable Updated' : 'New Timetable Set',
            'message' => ($this->action === 'updated'
                ? 'The timetable for '
                : 'A new timetable has been set for ') . $this->subject->code . ' - ' . $this->subject->name,
            'subject_id' => $this->subject->id,
        ];
    }
}
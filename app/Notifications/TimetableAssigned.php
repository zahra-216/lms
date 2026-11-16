<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TimetableAssigned extends Notification
{
    use Queueable;

    /** @param string $label e.g. "Business Management / Degree / Semester 4"; $action 'assigned' or 'updated' */
    public function __construct(protected string $label, protected string $action = 'assigned') {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'timetable',
            'title' => $this->action === 'updated' ? 'Timetable Updated' : 'New Timetable Set',
            'message' => ($this->action === 'updated' ? 'The timetable for ' : 'You have classes in the new timetable for ').$this->label.($this->action === 'updated' ? ' was changed.' : '.'),
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\DatabaseMessage;

class AssignmentSubmitted extends Notification
{
    protected $student;
    protected $assignment;

    public function __construct($student, $assignment)
    {
        $this->student = $student;
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['database']; // important
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Assignment Submission',
            'message' => $this->student->name . ' submitted ' . $this->assignment->title,
        ];
    }
}
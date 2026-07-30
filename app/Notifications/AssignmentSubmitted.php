<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentSubmitted extends Notification
{
    use Queueable;

    protected $student;
    protected $assignment;

    public function __construct($student, $assignment)
    {
        $this->student = $student;
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Assignment Submission',
            'message' => $this->student->name . ' submitted ' . $this->assignment->title,
            'link' => route('admin.assignments.submissions', $this->assignment->id),
        ];
    }
}
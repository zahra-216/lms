<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

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
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Assignment Submission',
            'message' => $this->student->name . ' submitted ' . $this->assignment->title,
            'link' => route('admin.assignments.submissions', $this->assignment->id),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Assignment Submission - ' . $this->assignment->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->student->name . ' has submitted an assignment.')
            ->line('Assignment: ' . $this->assignment->title)
            ->line('Subject: ' . ($this->assignment->subject->name ?? 'N/A'))
            ->action('View Submission', route('admin.assignments.submissions', $this->assignment->id))
            ->line('Please review it at your earliest convenience.');
    }
}
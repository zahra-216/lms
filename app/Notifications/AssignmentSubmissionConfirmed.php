<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AssignmentSubmissionConfirmed extends Notification
{
    use Queueable;

    protected $assignment;

    public function __construct($assignment)
    {
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Assignment Submitted Successfully - ' . $this->assignment->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have successfully submitted your assignment.')
            ->line('Assignment: ' . $this->assignment->title)
            ->line('Subject: ' . ($this->assignment->subject->name ?? 'N/A'))
            ->line('Submitted at: ' . now()->format('d M Y, h:i A'))
            ->line('Thank you!');
    }
}
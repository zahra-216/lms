<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * E-mail to the campus inbox (config mail.submissions_to) whenever a student hands in an assignment.
 * Sent on demand: Notification::route('mail', address)->notify(...)
 */
class AssignmentSubmittedMail extends Notification
{
    public function __construct(private $student, private $assignment)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New assignment submission: '.$this->assignment->title)
            ->greeting('New assignment submission')
            ->line('Student: '.$this->student->name.' ('.$this->student->registration_no.')')
            ->line('Assignment: '.$this->assignment->title)
            ->line('Subject: '.($this->assignment->subject->name ?? 'N/A'))
            ->line('Submitted at: '.now()->format('d M Y, h:i A'))
            ->action('View submissions', route('admin.assignments.submissions', $this->assignment->id));
    }
}

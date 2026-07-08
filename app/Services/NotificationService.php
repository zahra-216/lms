<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationSent;
use App\Models\Assignment;
use App\Models\Submission;

class NotificationService
{
    public function notifyAssignmentCreated(Assignment $assignment)
    {
        $users = $assignment->subject->users;

        foreach ($users as $user) {
            $notification = Notification::create([
                'user_id' => $user->id,
                'type' => 'assignment',
                'title' => 'New Assignment: ' . $assignment->title,
                'message' => 'A new assignment has been posted for ' . $assignment->subject->name,
                'link' => route('assignments.show', $assignment->id),
                'is_read' => false,
            ]);
            event(new NotificationSent($notification));
        }
    }

    public function notifySubmissionReceived(Submission $submission)
    {
        $instructor = $submission->assignment->subject->instructor;
        if (!$instructor) return;

        $notification = Notification::create([
            'user_id' => $instructor->id,
            'type' => 'submission',
            'title' => 'New Submission: ' . $submission->assignment->title,
            'message' => $submission->user->name . ' submitted the assignment.',
            'link' => route('submissions.show', $submission->id),
            'is_read' => false,
        ]);
        event(new NotificationSent($notification));
    }

    public function notifySubmissionGraded(Submission $submission)
    {
        $user = $submission->user;

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'grade',
            'title' => 'Submission Graded: ' . $submission->assignment->title,
            'message' => 'Your submission has been graded. Check your score.',
            'link' => route('submissions.show', $submission->id),
            'is_read' => false,
        ]);
        event(new NotificationSent($notification));
    }

    public function getUnreadCount(User $user)
    {
        return $user->notifications()->where('is_read', false)->count();
    }

    public function markAsRead(Notification $notification)
    {
        $notification->update(['is_read' => true]);
    }

    public function markAllAsRead(User $user)
    {
        return $user->notifications()->where('is_read', false)->update(['is_read' => true]);
    }
}
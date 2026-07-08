<?php

namespace App\Events;

use App\Models\Submission;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubmissionReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Submission $submission;

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
    }

    public function broadcastOn(): array
    {
        $this->submission->load([
            'user',
            'assignment.subject'
        ]);

        return [
            new PrivateChannel(
                'instructor.' . $this->submission->assignment->subject->instructor_id
            ),

            new PresenceChannel(
                'subject.' . $this->submission->assignment->subject_id
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'submission.received';
    }

    public function broadcastWith(): array
    {
        return [
            'submission' => $this->submission->only(['id','submitted_at','is_late']),
            'student' => $this->submission->user->only(['id','name']),
            'assignment' => $this->submission->assignment->only(['id','title']),
            'subject' => $this->submission->assignment->subject->only(['id','name']),
            'timestamp' => now()->toISOString(),
        ];
    }
}
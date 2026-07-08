<?php

namespace App\Events;

use App\Models\Assignment;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignmentUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Assignment $assignment;
    public string $action;

    public function __construct(Assignment $assignment, string $action)
    {
        $this->assignment = $assignment;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('subject.' . $this->assignment->subject_id);
    }

    public function broadcastAs()
    {
        return 'assignment.' . $this->action;
    }

    public function broadcastWith()
    {
        return [
            'assignment_id' => $this->assignment->id,
            'title' => $this->assignment->title,
            'action' => $this->action,
            'timestamp' => now()->toISOString(),
        ];
    }
}
<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignmentCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $assignment;
    public $studentIds;

    public function __construct($assignment, $studentIds)
    {
        $this->assignment = $assignment;
        $this->studentIds = $studentIds;
    }

    public function broadcastOn()
    {
        return collect($this->studentIds)->map(function ($id) {
            return new PrivateChannel('student.' . $id);
        })->toArray();
    }

    public function broadcastAs()
    {
        return 'assignment.created';
    }

    public function broadcastWith()
    {
        return [
            'message' => 'New Assignment: ' . $this->assignment->title,
            'assignment_id' => $this->assignment->id
        ];
    }
}
<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list announcements
    }

    public function view(User $user, Announcement $announcement): bool
    {
        // Users can view if they're enrolled in the course or are the instructor/admin
        return $user->role?->name === 'admin' || 
               $user->id === $announcement->course->instructor_id ||
               $announcement->subject->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'admin' || $user->role?->name === 'instructor';
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->role?->name === 'admin' || $user->id === $announcement->subject->instructor_id;
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->role?->name === 'admin' || $user->id === $announcement->subject->instructor_id;
    }
}
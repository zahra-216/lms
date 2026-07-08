<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list course contents
    }

    public function view(User $user, Note $note): bool
    {
        // Users can view if they're enrolled in the course or are the instructor/admin
        return $user->role?->name === 'admin' || 
               $user->id === $note->Subject->instructor_id ||
               $note->Subject->users()->where('user_id', $user->id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'admin' || $user->role?->name === 'instructor';
    }

    public function update(User $user, Note $note): bool
    {
        return $user->role?->name === 'admin' || $user->id === $note->subject->instructor_id;
    }

    public function delete(User $user, Note $note): bool
    {
        return $user->role?->name === 'admin' || $user->id === $note->subject->instructor_id;
    }
}
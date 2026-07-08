<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user) {
        return $user->hasRole('admin') || $user->hasRole('instructor');
    }

    public function view(User $user, Assignment $assignment) {
        return $user->hasRole('admin') || $user->id === $assignment->subject->instructor_id;
    }

    public function create(User $user) {
        return $user->hasRole('admin') || $user->hasRole('instructor');
    }

    public function update(User $user, Assignment $assignment) {
        return $user->hasRole('admin') || $user->id === $assignment->subject->instructor_id;
    }

    public function delete(User $user, Assignment $assignment) {
        return $user->hasRole('admin') || $user->id === $assignment->subject->instructor_id;
    }
}
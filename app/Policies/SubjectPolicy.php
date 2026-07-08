<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list courses
    }

    public function view(User $user, Subject $subject): bool
    {
        return true; // All authenticated users can view courses
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'admin' || $user->role?->name === 'instructor';
    }

    public function update(User $user, Subject $subject): bool
    {
        return $user->role?->name === 'admin' || $user->id === $subject->instructor_id;
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->role?->name === 'admin' || $user->id === $subject->instructor_id;
    }
}

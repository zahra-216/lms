<?php

namespace App\Policies;

use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can list enrollments
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        // Users can view their own enrollments, or admins/instructors can view all
        return $user->id === $enrollment->user_id ||
               $user->role?->name === 'admin' ||
               $user->id === $enrollment->course->instructor_id;
    }

    public function create(User $user): bool
    {
        return $user->role?->name === 'admin' || $user->role?->name === 'student';
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        // Users can unenroll themselves, or admins/instructors can remove any enrollment
        return $user->id === $enrollment->user_id ||
               $user->role?->name === 'admin' ||
               $user->id === $enrollment->course->instructor_id;
    }
}

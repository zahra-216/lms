<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function update(User $user, Submission $submission) {
        return $user->hasRole('admin') || $user->id === $submission->assignment->subject->instructor_id;
    }

    public function view(User $user, Submission $submission) {
        return $user->hasRole('admin') || $user->id === $submission->assignment->subject->instructor_id || $user->id === $submission->user_id;
    }
}
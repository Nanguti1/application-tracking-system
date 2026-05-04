<?php

namespace App\Policies;

use App\Models\Interview;
use App\Models\User;

class InterviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_interviews');
    }

    public function view(User $user, Interview $interview): bool
    {
        return $user->hasPermission('view_interviews');
    }

    public function schedule(User $user): bool
    {
        return $user->hasPermission('schedule_interviews');
    }

    public function reschedule(User $user, Interview $interview): bool
    {
        return $user->hasPermission('reschedule_interviews');
    }

    public function cancel(User $user, Interview $interview): bool
    {
        return $user->hasPermission('cancel_interviews');
    }

    public function submitFeedback(User $user, Interview $interview): bool
    {
        return $user->hasPermission('submit_interview_feedback');
    }
}

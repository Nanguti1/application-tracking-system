<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_applications');
    }

    public function view(User $user, JobApplication $application): bool
    {
        // Candidate can view their own application
        if ($user->id === $application->user_id) {
            return true;
        }

        return $user->hasPermission('view_applications');
    }

    public function shortlist(User $user, JobApplication $application): bool
    {
        return $user->hasPermission('shortlist_applications');
    }

    public function reject(User $user, JobApplication $application): bool
    {
        return $user->hasPermission('reject_applications');
    }

    public function updateStatus(User $user, JobApplication $application): bool
    {
        return $user->hasPermission('update_application_status');
    }

    public function export(User $user): bool
    {
        return $user->hasPermission('export_applications');
    }
}

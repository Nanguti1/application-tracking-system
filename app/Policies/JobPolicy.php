<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_jobs', 'edit_jobs', 'delete_jobs']);
    }

    public function view(User $user, Job $job): bool
    {
        return $user->hasAnyPermission(['view_jobs', 'edit_jobs']);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_jobs');
    }

    public function update(User $user, Job $job): bool
    {
        return $user->hasPermission('edit_jobs');
    }

    public function delete(User $user, Job $job): bool
    {
        return $user->hasPermission('delete_jobs');
    }

    public function publish(User $user, Job $job): bool
    {
        return $user->hasPermission('publish_jobs');
    }

    public function archive(User $user, Job $job): bool
    {
        return $user->hasPermission('archive_jobs');
    }

    public function duplicate(User $user, Job $job): bool
    {
        return $user->hasPermission('duplicate_jobs');
    }

    public function viewAnalytics(User $user, Job $job): bool
    {
        return $user->hasPermission('view_job_analytics');
    }
}

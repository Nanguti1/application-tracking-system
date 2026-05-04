<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;

class ApplicationService
{
    public function submitApplication(array $data): JobApplication
    {
        // Check for existing application
        $existing = JobApplication::where('job_id', $data['job_id'])
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            throw new \Exception('You have already applied for this job.');
        }

        $application = JobApplication::create([
            'job_id' => $data['job_id'],
            'user_id' => auth()->id(),
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'cover_letter' => $data['cover_letter'] ?? null,
            'linkedin_profile' => $data['linkedin_profile'] ?? null,
            'portfolio_url' => $data['portfolio_url'] ?? null,
            'years_of_experience' => $data['years_of_experience'],
            'education_level' => $data['education_level'] ?? null,
            'expected_salary' => $data['expected_salary'] ?? null,
            'location' => $data['location'] ?? null,
            'availability_date' => $data['availability_date'] ?? null,
            'status' => 'applied',
        ]);

        // Update job application count
        $application->job->increment('applications_count');

        return $application;
    }

    public function updateApplicationStatus(JobApplication $application, string $status, ?string $reason = null): JobApplication
    {
        $oldStatus = $application->status;
        
        $application->update([
            'status' => $status,
            'rejection_reason' => $reason,
        ]);

        // Update counters
        if ($oldStatus !== 'shortlisted' && $status === 'shortlisted') {
            $application->job->increment('shortlisted_count');
        } elseif ($oldStatus === 'shortlisted' && $status !== 'shortlisted') {
            $application->job->decrement('shortlisted_count');
        }

        if ($status === 'rejected') {
            $application->update(['rejected_at' => now()]);
            $application->job->increment('rejected_count');
        }

        if ($application->user) {
            $application->user->notify(new ApplicationStatusChanged($application->fresh('job'), $oldStatus, $status));
        }

        return $application;
    }

    public function getApplicationsForJob(Job $job, ?string $status = null, array $orderBy = ['created_at', 'desc'])
    {
        $query = $job->applications();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderBy($orderBy[0], $orderBy[1])->get();
    }

    public function getTopShortlistedApplications(Job $job, int $limit = 10)
    {
        return $job->applications()
            ->whereIn('status', ['shortlisted', 'interview', 'final_interview', 'offer', 'hired'])
            ->orderByDesc('match_score')
            ->limit($limit)
            ->get();
    }

    public function shortlistApplications(Job $job, int $count): void
    {
        // Get top ranked applications
        $toShortlist = $job->applications()
            ->where('status', 'applied')
            ->orderByDesc('ranking_score')
            ->limit($count)
            ->get();

        foreach ($toShortlist as $application) {
            $this->updateApplicationStatus($application, 'shortlisted');
        }

        // Reject the rest
        $job->applications()
            ->where('status', 'applied')
            ->update(['status' => 'rejected', 'rejected_at' => now()]);
    }

    public function withdrawApplication(JobApplication $application): JobApplication
    {
        if ($application->status !== 'rejected') {
            $application->update(['status' => 'rejected']);
            
            if ($application->status === 'shortlisted') {
                $application->job->decrement('shortlisted_count');
            }
        }

        return $application;
    }

    public function filterApplications(Job $job, array $filters)
    {
        $query = $job->applications();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['min_score'])) {
            $query->where('match_score', '>=', $filters['min_score']);
        }

        if (!empty($filters['experience_level'])) {
            $experienceLevels = [
                'junior' => [0, 2],
                'mid' => [3, 5],
                'senior' => [6, PHP_INT_MAX],
            ];
            [$min, $max] = $experienceLevels[$filters['experience_level']] ?? [0, PHP_INT_MAX];
            $query->whereBetween('years_of_experience', [$min, $max]);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        return $query->orderByDesc('created_at')->paginate(20);
    }
}

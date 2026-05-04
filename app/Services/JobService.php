<?php

namespace App\Services;

use App\Models\Job;
use App\Models\EmailTemplate;
use Illuminate\Pagination\Paginator;

class JobService
{
    public function createJob(array $data): Job
    {
        $job = Job::create($data);

        // Create default email templates for this job
        $this->createDefaultEmailTemplates($job);

        // Create default interview stages for rolling interviews
        if ($data['interview_type'] === 'rolling') {
            $this->createDefaultInterviewStages($job);
        }

        return $job;
    }

    public function updateJob(Job $job, array $data): Job
    {
        $job->update($data);
        return $job;
    }

    public function publishJob(Job $job): Job
    {
        $job->update(['status' => 'published']);
        return $job;
    }

    public function unpublishJob(Job $job): Job
    {
        $job->update(['status' => 'draft']);
        return $job;
    }

    public function closeJob(Job $job): Job
    {
        $job->update(['status' => 'closed']);
        return $job;
    }

    public function archiveJob(Job $job): Job
    {
        $job->update(['status' => 'archived']);
        return $job;
    }

    public function duplicateJob(Job $original): Job
    {
        $jobData = $original->replicate()->toArray();
        unset($jobData['id']);
        $jobData['status'] = 'draft';
        $jobData['applications_count'] = 0;
        $jobData['shortlisted_count'] = 0;
        $jobData['rejected_count'] = 0;

        $job = $this->createJob($jobData);

        return $job;
    }

    private function createDefaultEmailTemplates(Job $job): void
    {
        $defaults = EmailTemplate::getDefaultTemplates();

        foreach ($defaults as $type => $template) {
            EmailTemplate::firstOrCreate([
                'job_id' => $job->id,
                'type' => $type,
            ], [
                'name' => ucfirst(str_replace('_', ' ', $type)) . ' - ' . $job->title,
                'subject' => $template['subject'],
                'body' => $template['body'],
                'placeholders_available' => json_encode([
                    'candidate_name',
                    'job_title',
                    'company_name',
                    'interview_date',
                    'interview_time',
                    'interview_duration',
                    'start_date',
                ]),
            ]);
        }
    }

    private function createDefaultInterviewStages(Job $job): void
    {
        $stages = [
            ['name' => 'CV Review', 'order' => 1, 'duration_minutes' => 15],
            ['name' => 'Phone Screening', 'order' => 2, 'duration_minutes' => 30],
            ['name' => 'Technical Interview', 'order' => 3, 'duration_minutes' => 60],
            ['name' => 'HR Interview', 'order' => 4, 'duration_minutes' => 45],
            ['name' => 'Final Interview', 'order' => 5, 'duration_minutes' => 60],
        ];

        foreach ($stages as $stage) {
            $job->interviewStages()->create($stage);
        }
    }

    public function getPublishedJobs(int $perPage = 12): Paginator
    {
        return Job::where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('deadline_at')
                    ->orWhere('deadline_at', '>', now());
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function searchJobs(string $search, int $perPage = 12): Paginator
    {
        return Job::where('status', 'published')
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->where(function ($query) {
                $query->whereNull('deadline_at')
                    ->orWhere('deadline_at', '>', now());
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function filterJobs(array $filters, int $perPage = 12): Paginator
    {
        $query = Job::where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('deadline_at')
                    ->orWhere('deadline_at', '>', now());
            });

        if (!empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', "%{$filters['location']}%");
        }

        if (!empty($filters['employment_type'])) {
            $query->where('employment_type', $filters['employment_type']);
        }

        if (!empty($filters['salary_min'])) {
            $query->where('salary_max', '>=', $filters['salary_min']);
        }

        if (!empty($filters['salary_max'])) {
            $query->where('salary_min', '<=', $filters['salary_max']);
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    public function getJobsForDashboard(User $user)
    {
        $query = Job::query();

        if ($user->hasRole('Recruiter')) {
            // Recruiters see all jobs (they manage applications)
        } elseif ($user->hasRole('Interviewer')) {
            // Interviewers see jobs they have scheduled interviews for
            $query->whereHas('interviews', function ($q) use ($user) {
                $q->where('interviewer_email', $user->email);
            });
        }

        return $query->orderByDesc('created_at')->get();
    }
}

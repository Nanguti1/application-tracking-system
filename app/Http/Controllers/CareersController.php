<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Services\JobService;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CareersController extends Controller
{
    public function __construct(
        private JobService $jobService,
        private ApplicationService $applicationService
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $filters = $request->all(['department', 'location', 'employment_type', 'salary_min', 'salary_max']);

        if ($search) {
            $jobs = $this->jobService->searchJobs($search);
        } elseif (!empty(array_filter($filters))) {
            $jobs = $this->jobService->filterJobs($filters);
        } else {
            $jobs = $this->jobService->getPublishedJobs();
        }

        return Inertia::render('Careers/Index', [
            'jobs' => $jobs,
            'search' => $search,
            'filters' => $filters,
            'departments' => Job::where('status', 'published')->distinct()->pluck('department'),
            'locations' => Job::where('status', 'published')->distinct()->pluck('location'),
        ]);
    }

    public function show(Job $job): Response
    {
        // Only show published jobs on public portal
        if ($job->status !== 'published') {
            abort(404);
        }

        // Check if user already applied
        $userApplied = false;
        if (auth()->check()) {
            $userApplied = JobApplication::where('job_id', $job->id)
                ->where('user_id', auth()->id())
                ->exists();
        }

        return Inertia::render('Careers/Show', [
            'job' => $job->load(['interviewStages']),
            'userApplied' => $userApplied,
            'requiresAuth' => true,
        ]);
    }

    public function apply(Job $job, Request $request)
    {
        // Only allow applications for published jobs
        if (!$job->canAcceptApplications()) {
            return redirect()->route('careers.show', $job)->with('error', 'This job is no longer accepting applications.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'cover_letter' => 'nullable|string|max:5000',
            'linkedin_profile' => 'nullable|url',
            'portfolio_url' => 'nullable|url',
            'years_of_experience' => 'required|integer|min:0|max:100',
            'education_level' => 'nullable|string|max:255',
            'expected_salary' => 'nullable|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'availability_date' => 'nullable|date',
        ]);

        $validated['job_id'] = $job->id;

        try {
            $application = $this->applicationService->submitApplication($validated);

            // Dispatch CV parsing job if file is uploaded
            if ($request->hasFile('resume')) {
                // This will be handled in a separate CV upload endpoint
            }

            return redirect()->route('applications.show', $application)
                ->with('success', 'Application submitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
}

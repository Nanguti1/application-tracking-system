<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Services\JobService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    public function __construct(private JobService $jobService) {}

    public function index(): Response
    {
        $this->authorize('viewAny', Job::class);

        $jobs = Job::orderByDesc('created_at')->paginate(20);

        return Inertia::render('Admin/Jobs/Index', [
            'jobs' => $jobs,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Job::class);

        return Inertia::render('Admin/Jobs/Create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary,internship',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'deadline_at' => 'nullable|date',
            'interview_date_start' => 'nullable|date',
            'interview_date_end' => 'nullable|date',
            'candidates_to_shortlist' => 'required|integer|min:1',
            'interview_type' => 'required|in:fixed,rolling',
        ]);

        $job = $this->jobService->createJob($validated);

        return redirect()->route('jobs.show', $job)->with('success', 'Job created successfully.');
    }

    public function show(Job $job): Response
    {
        $this->authorize('view', $job);

        $job->load(['applications', 'interviewStages', 'emailTemplates']);

        return Inertia::render('Admin/Jobs/Show', [
            'job' => $job,
            'applicationsCount' => $job->applications_count,
            'shortlistedCount' => $job->shortlisted_count,
            'rejectedCount' => $job->rejected_count,
        ]);
    }

    public function edit(Job $job): Response
    {
        $this->authorize('update', $job);

        return Inertia::render('Admin/Jobs/Edit', [
            'job' => $job,
        ]);
    }

    public function update(Request $request, Job $job)
    {
        $this->authorize('update', $job);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|in:full_time,part_time,contract,temporary,internship',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'deadline_at' => 'nullable|date',
            'interview_date_start' => 'nullable|date',
            'interview_date_end' => 'nullable|date',
            'candidates_to_shortlist' => 'required|integer|min:1',
            'interview_type' => 'required|in:fixed,rolling',
        ]);

        $this->jobService->updateJob($job, $validated);

        return redirect()->route('jobs.show', $job)->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job deleted successfully.');
    }

    public function publish(Job $job)
    {
        $this->authorize('publish', $job);

        $this->jobService->publishJob($job);

        return redirect()->route('jobs.show', $job)->with('success', 'Job published successfully.');
    }

    public function unpublish(Job $job)
    {
        $this->authorize('publish', $job);

        $this->jobService->unpublishJob($job);

        return redirect()->route('jobs.show', $job)->with('success', 'Job unpublished successfully.');
    }

    public function close(Job $job)
    {
        $this->authorize('publish', $job);

        $this->jobService->closeJob($job);

        return redirect()->route('jobs.show', $job)->with('success', 'Job closed successfully.');
    }

    public function archive(Job $job)
    {
        $this->authorize('archive', $job);

        $this->jobService->archiveJob($job);

        return redirect()->route('jobs.index')->with('success', 'Job archived successfully.');
    }

    public function duplicate(Job $job)
    {
        $this->authorize('duplicate', $job);

        $newJob = $this->jobService->duplicateJob($job);

        return redirect()->route('jobs.edit', $newJob)->with('success', 'Job duplicated successfully.');
    }
}

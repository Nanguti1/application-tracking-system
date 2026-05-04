<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    public function __construct(private ApplicationService $applicationService) {}

    public function index(Job $job): Response
    {
        $this->authorize('viewAny', JobApplication::class);

        $applications = $this->applicationService->filterApplications($job, request()->all());

        return Inertia::render('Admin/Applications/Index', [
            'job' => $job,
            'applications' => $applications,
        ]);
    }

    public function show(JobApplication $application): Response
    {
        $this->authorize('view', $application);

        $application->load(['resume', 'interviews', 'job']);

        return Inertia::render('Admin/Applications/Show', [
            'application' => $application,
        ]);
    }

    public function shortlist(JobApplication $application)
    {
        $this->authorize('shortlist', $application);

        $this->applicationService->updateApplicationStatus($application, 'shortlisted');

        return redirect()->back()->with('success', 'Application shortlisted.');
    }

    public function reject(JobApplication $application, Request $request)
    {
        $this->authorize('reject', $application);

        $this->applicationService->updateApplicationStatus(
            $application,
            'rejected',
            $request->input('reason')
        );

        return redirect()->back()->with('success', 'Application rejected.');
    }

    public function advance(JobApplication $application, Request $request)
    {
        $this->authorize('updateStatus', $application);

        $status = $request->input('status');
        $this->applicationService->updateApplicationStatus($application, $status);

        return redirect()->back()->with('success', 'Application status updated.');
    }

    public function myApplications(): Response
    {
        $applications = auth()->user()->jobApplications()->with('job')->paginate(20);

        return Inertia::render('Candidate/Applications', [
            'applications' => $applications,
        ]);
    }

    public function myProfile(): Response
    {
        return Inertia::render('Candidate/Profile', [
            'user' => auth()->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
        ]);

        auth()->user()->update($validated);

        return redirect()->back()->with('success', 'Profile updated.');
    }
}

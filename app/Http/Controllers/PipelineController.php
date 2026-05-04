<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PipelineController extends Controller
{
    public function show(Job $job): Response
    {
        $this->authorize('view', JobApplication::class);

        // Get applications grouped by status
        $statuses = [
            'applied',
            'screening',
            'shortlisted',
            'interview',
            'final_interview',
            'offer',
            'hired',
            'rejected',
        ];

        $pipeline = [];
        foreach ($statuses as $status) {
            $pipeline[$status] = $job->applications()
                ->where('status', $status)
                ->orderBy('created_at')
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'full_name' => $app->full_name,
                        'email' => $app->email,
                        'match_score' => $app->match_score,
                        'ranking_score' => $app->ranking_score,
                        'status' => $app->status,
                    ];
                });
        }

        return Inertia::render('Admin/Pipeline', [
            'job' => $job,
            'pipeline' => $pipeline,
        ]);
    }

    public function moveApplication(Job $job, Request $request)
    {
        $this->authorize('manage', JobApplication::class);

        $validated = $request->validate([
            'application_id' => 'required|exists:job_applications,id',
            'status' => 'required|in:applied,screening,shortlisted,interview,final_interview,offer,hired,rejected',
        ]);

        $application = JobApplication::find($validated['application_id']);

        if ($application->job_id !== $job->id) {
            abort(403);
        }

        $application->update(['status' => $validated['status']]);

        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\InterviewStage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InterviewController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Interview::class);

        $interviews = Interview::with(['job', 'jobApplication'])
            ->orderBy('scheduled_at')
            ->paginate(20);

        return Inertia::render('Admin/Interviews/Index', [
            'interviews' => $interviews,
        ]);
    }

    public function show(Interview $interview): Response
    {
        $this->authorize('view', $interview);

        $interview->load(['job', 'jobApplication', 'interviewStage']);

        return Inertia::render('Admin/Interviews/Show', [
            'interview' => $interview,
        ]);
    }

    public function scheduleFromPool(Job $job, Request $request)
    {
        $this->authorize('schedule', Interview::class);

        $validated = $request->validate([
            'application_ids' => 'required|array|min:1',
            'interview_stage_id' => 'required|exists:interview_stages,id',
            'date' => 'required|date|after:today',
            'time' => 'nullable|date_format:H:i',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i|after:break_start',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|url',
        ]);

        $stage = InterviewStage::find($validated['interview_stage_id']);

        $scheduleTimes = [];
        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            $cursor = now()->parse($validated['date'] . ' ' . $validated['start_time']);
            $end = now()->parse($validated['date'] . ' ' . $validated['end_time']);
            $breakStart = !empty($validated['break_start']) ? now()->parse($validated['date'] . ' ' . $validated['break_start']) : null;
            $breakEnd = !empty($validated['break_end']) ? now()->parse($validated['date'] . ' ' . $validated['break_end']) : null;

            while ($cursor->copy()->addMinutes($stage->duration_minutes)->lte($end)) {
                if ($breakStart && $breakEnd && $cursor->betweenIncluded($breakStart, $breakEnd->copy()->subMinute())) {
                    $cursor = $breakEnd->copy();
                    continue;
                }
                $scheduleTimes[] = $cursor->copy();
                $cursor->addMinutes($stage->duration_minutes);
            }
        } elseif (!empty($validated['time'])) {
            $scheduleTimes[] = now()->parse($validated['date'] . ' ' . $validated['time']);
        }

        foreach ($validated['application_ids'] as $index => $appId) {
            $application = JobApplication::find($appId);
            
            if ($application->job_id === $job->id) {
                $scheduledAt = $scheduleTimes[$index] ?? ($scheduleTimes[0] ?? now()->parse($validated['date'] . ' 09:00'));
                Interview::create([
                    'job_id' => $job->id,
                    'job_application_id' => $application->id,
                    'interview_stage_id' => $stage->id,
                    'scheduled_at' => $scheduledAt,
                    'duration_minutes' => $stage->duration_minutes,
                    'location' => $validated['location'],
                    'meeting_link' => $validated['meeting_link'],
                    'status' => 'scheduled',
                ]);

                // Update application status
                $application->update(['status' => 'interview']);
            }
        }

        return redirect()->back()->with('success', 'Interviews scheduled successfully.');
    }

    public function store(Request $request)
    {
        $this->authorize('schedule', Interview::class);

        $validated = $request->validate([
            'job_application_id' => 'required|exists:job_applications,id',
            'interview_stage_id' => 'required|exists:interview_stages,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15',
            'interview_type' => 'nullable|string',
            'interviewer_name' => 'nullable|string',
            'interviewer_email' => 'nullable|email',
            'location' => 'nullable|string',
            'meeting_link' => 'nullable|url',
        ]);

        $application = JobApplication::find($validated['job_application_id']);

        $interview = Interview::create([
            'job_id' => $application->job_id,
            ...$validated,
            'status' => 'scheduled',
        ]);

        return redirect()->route('interviews.show', $interview)->with('success', 'Interview scheduled.');
    }

    public function reschedule(Interview $interview, Request $request)
    {
        $this->authorize('reschedule', $interview);

        $validated = $request->validate([
            'scheduled_at' => 'required|date|after:now',
            'reason' => 'nullable|string',
        ]);

        $interview->update([
            'scheduled_at' => $validated['scheduled_at'],
            'status' => 'rescheduled',
            'rescheduled_at' => now(),
            'reschedule_reason' => $validated['reason'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Interview rescheduled.');
    }

    public function cancel(Interview $interview, Request $request)
    {
        $this->authorize('cancel', $interview);

        $interview->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Interview cancelled.');
    }

    public function submitFeedback(Interview $interview, Request $request)
    {
        $this->authorize('submitFeedback', $interview);

        $validated = $request->validate([
            'feedback' => 'required|string',
            'score' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:completed,no_show',
        ]);

        $interview->update([
            'feedback' => $validated['feedback'],
            'interviewer_score' => $validated['score'],
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Feedback submitted.');
    }
}

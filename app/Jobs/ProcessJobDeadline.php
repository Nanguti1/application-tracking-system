<?php

namespace App\Jobs;

use App\Models\Job;
use App\Services\ApplicationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessJobDeadline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Job $job) {}

    public function handle(ApplicationService $applicationService): void
    {
        // Close the job
        $this->job->update(['status' => 'closed']);

        // Get the candidate count to shortlist
        $countToShortlist = $this->job->candidates_to_shortlist;

        // Automatically shortlist top candidates by ranking score
        $applicationService->shortlistApplications($this->job, $countToShortlist);

        // Update job status to interviewing
        $this->job->update(['status' => 'interviewing']);
    }
}

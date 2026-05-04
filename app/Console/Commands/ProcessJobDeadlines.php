<?php

namespace App\Console\Commands;

use App\Models\Job;
use App\Jobs\ProcessJobDeadline;
use Illuminate\Console\Command;

class ProcessJobDeadlines extends Command
{
    protected $signature = 'ats:process-deadlines';

    protected $description = 'Process job deadlines and automatically shortlist candidates';

    public function handle()
    {
        $jobs = Job::where('status', 'published')
            ->where('deadline_at', '<=', now())
            ->get();

        foreach ($jobs as $job) {
            ProcessJobDeadline::dispatch($job);
            $this->info("Processing deadline for job: {$job->title}");
        }

        $this->info("Processed {$jobs->count()} job deadlines");
    }
}

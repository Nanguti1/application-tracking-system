<?php

namespace App\Jobs;

use App\Models\Resume;
use App\Services\CVParsingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ParseAndScoreApplicationResume implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $resumeId) {}

    public function handle(CVParsingService $cvParsingService): void
    {
        $resume = Resume::with('jobApplication.job')->find($this->resumeId);

        if (!$resume || !$resume->jobApplication || !$resume->jobApplication->job) {
            return;
        }

        if (!Storage::disk('private')->exists($resume->file_path)) {
            return;
        }

        $absolutePath = Storage::disk('private')->path($resume->file_path);
        $parsed = $cvParsingService->parseCVFromFile($absolutePath, $resume->mime_type);
        $resume->parsed_data = $parsed;
        $scores = $cvParsingService->scoreAgainstJob($resume, $resume->jobApplication->job);

        $resume->update([
            'raw_text' => $parsed['raw_text'] ?? null,
            'parsed_data' => $parsed,
            'extracted_email' => $parsed['contact']['emails'] ?? [],
            'extracted_phone' => $parsed['contact']['phones'] ?? [],
        ]);

        $resume->jobApplication->update([
            'cv_parsed' => true,
            'match_score' => $scores['total_score'],
            'ranking_score' => $scores['total_score'],
            'match_details' => $scores,
        ]);
    }
}

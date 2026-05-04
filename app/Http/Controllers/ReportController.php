<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
    public function index(): InertiaResponse
    {
        $now = now();
        $monthStart = $now->copy()->startOfMonth();

        $applicationsQuery = JobApplication::query();

        return Inertia::render('Admin/Reports/Index', [
            'totals' => [
                'applications' => (clone $applicationsQuery)->count(),
                'hired' => (clone $applicationsQuery)->where('status', 'hired')->count(),
                'rejected' => (clone $applicationsQuery)->where('status', 'rejected')->count(),
                'inProcess' => (clone $applicationsQuery)->whereIn('status', ['shortlisted', 'interview', 'final_interview', 'offer'])->count(),
                'monthApplications' => (clone $applicationsQuery)->whereDate('created_at', '>=', $monthStart)->count(),
            ],
            'statusBreakdown' => $this->statusBreakdown(),
            'topJobs' => $this->topJobs(),
            'applicationTrend' => $this->applicationTrend(),
        ]);
    }

    public function exportCsv(): Response
    {
        $rows = JobApplication::with('job:id,title')->orderByDesc('created_at')->limit(5000)->get();
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="applications-report.csv"'];

        $callback = static function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'job', 'candidate', 'email', 'status', 'match_score', 'ranking_score', 'experience_years', 'location', 'created_at']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->job?->title,
                    $row->full_name,
                    $row->email,
                    $row->status,
                    $row->match_score,
                    $row->ranking_score,
                    $row->years_of_experience,
                    $row->location,
                    $row->created_at,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function statusBreakdown(): array
    {
        return JobApplication::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($item): array => [
                'status' => (string) $item->status,
                'total' => (int) $item->total,
            ])
            ->all();
    }

    private function topJobs(): array
    {
        return JobApplication::query()
            ->selectRaw('job_id, count(*) as total, avg(match_score) as avg_score')
            ->with('job:id,title')
            ->groupBy('job_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($item): array => [
                'jobTitle' => (string) ($item->job?->title ?? 'Unknown Job'),
                'total' => (int) $item->total,
                'averageScore' => round((float) $item->avg_score, 2),
            ])
            ->all();
    }

    private function applicationTrend(): array
    {
        $start = now()->subDays(6)->startOfDay();

        /** @var array<string, int> $counts */
        $counts = JobApplication::query()
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, count(*) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day')
            ->map(fn ($value): int => (int) $value)
            ->all();

        return collect(range(0, 6))
            ->map(function (int $offset) use ($start, $counts): array {
                $date = $start->copy()->addDays($offset);
                $key = $date->toDateString();

                return [
                    'date' => $key,
                    'label' => Carbon::parse($key)->format('M j'),
                    'total' => $counts[$key] ?? 0,
                ];
            })
            ->all();
    }
}

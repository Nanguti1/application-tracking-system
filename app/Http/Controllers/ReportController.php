<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ReportController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Admin/Reports/Index', [
            'totals' => [
                'applications' => JobApplication::count(),
                'hired' => JobApplication::where('status', 'hired')->count(),
                'rejected' => JobApplication::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function exportCsv(): Response
    {
        $rows = JobApplication::with('job:id,title')->orderByDesc('created_at')->limit(5000)->get();
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="applications-report.csv"'];

        $callback = static function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'job', 'candidate', 'email', 'status', 'match_score', 'ranking_score', 'created_at']);
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $row->id,
                    $row->job?->title,
                    $row->full_name,
                    $row->email,
                    $row->status,
                    $row->match_score,
                    $row->ranking_score,
                    $row->created_at,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_jobs' => Job::count(),
            'active_jobs' => Job::where('status', 'published')->count(),
            'total_applicants' => JobApplication::count(),
            'shortlisted' => JobApplication::whereIn('status', ['shortlisted', 'interview', 'final_interview', 'offer', 'hired'])->count(),
            'rejected' => JobApplication::where('status', 'rejected')->count(),
            'pending_interviews' => DB::table('interviews')->where('status', 'scheduled')->count(),
        ];

        // Hiring funnel data
        $funnel = [
            'applied' => JobApplication::where('status', 'applied')->count(),
            'screening' => JobApplication::where('status', 'screening')->count(),
            'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
            'interview' => JobApplication::where('status', 'interview')->count(),
            'final_interview' => JobApplication::where('status', 'final_interview')->count(),
            'offer' => JobApplication::where('status', 'offer')->count(),
            'hired' => JobApplication::where('status', 'hired')->count(),
        ];

        // Application trends (last 30 days)
        $trends = JobApplication::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->get();

        // Top jobs by applications
        $topJobs = Job::withCount('applications')
            ->orderByDesc('applications_count')
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'funnel' => $funnel,
            'trends' => $trends,
            'topJobs' => $topJobs,
        ]);
    }
}

<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CareersController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ReportController;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

// Public Careers Portal
Route::group(['prefix' => 'careers', 'as' => 'careers.'], function () {
    Route::get('/', [CareersController::class, 'index'])->name('index');
    Route::get('/{job}', [CareersController::class, 'show'])->name('show');
    Route::post('/{job}/apply', [CareersController::class, 'apply'])->middleware('auth')->name('apply');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    // Admin/HR Dashboard
    Route::middleware(['role:Super Admin|HR Admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    });

    // Jobs Management
    Route::middleware(['permission:view_jobs'])->group(function () {
        Route::resource('jobs', JobController::class);
        Route::post('jobs/{job}/publish', [JobController::class, 'publish'])->name('jobs.publish');
        Route::post('jobs/{job}/unpublish', [JobController::class, 'unpublish'])->name('jobs.unpublish');
        Route::post('jobs/{job}/close', [JobController::class, 'close'])->name('jobs.close');
        Route::post('jobs/{job}/archive', [JobController::class, 'archive'])->name('jobs.archive');
        Route::post('jobs/{job}/duplicate', [JobController::class, 'duplicate'])->name('jobs.duplicate');
    });

    // Applications Management
    Route::middleware(['permission:view_applications'])->group(function () {
        Route::get('jobs/{job}/applications', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    });

    Route::middleware(['permission:shortlist_applications'])->group(function () {
        Route::post('applications/{application}/shortlist', [ApplicationController::class, 'shortlist'])->name('applications.shortlist');
        Route::post('applications/{application}/reject', [ApplicationController::class, 'reject'])->name('applications.reject');
        Route::post('applications/{application}/advance', [ApplicationController::class, 'advance'])->name('applications.advance');
    });

    // Interviews Management
    Route::middleware(['permission:view_interviews'])->group(function () {
        Route::get('interviews', [InterviewController::class, 'index'])->name('interviews.index');
        Route::get('interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show');
    });

    Route::middleware(['permission:schedule_interviews'])->group(function () {
        Route::post('jobs/{job}/schedule-interviews', [InterviewController::class, 'scheduleFromPool'])->name('interviews.schedule-pool');
        Route::post('interviews', [InterviewController::class, 'store'])->name('interviews.store');
    });

    Route::middleware(['permission:reschedule_interviews'])->group(function () {
        Route::post('interviews/{interview}/reschedule', [InterviewController::class, 'reschedule'])->name('interviews.reschedule');
        Route::post('interviews/{interview}/cancel', [InterviewController::class, 'cancel'])->name('interviews.cancel');
    });

    Route::middleware(['permission:submit_interview_feedback'])->group(function () {
        Route::post('interviews/{interview}/feedback', [InterviewController::class, 'submitFeedback'])->name('interviews.feedback');
    });

    // Pipeline/Kanban View
    Route::middleware(['permission:view_pipeline'])->group(function () {
        Route::get('jobs/{job}/pipeline', [PipelineController::class, 'show'])->name('pipeline.show');
        Route::post('jobs/{job}/pipeline/move', [PipelineController::class, 'moveApplication'])->name('pipeline.move');
    });

    Route::middleware(['permission:view_jobs'])->group(function () {
        Route::get('jobs/{job}/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('jobs/{job}/email-templates/{template}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
        Route::put('jobs/{job}/email-templates/{template}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
    });

    Route::middleware(['role:Super Admin|HR Admin'])->group(function () {
        Route::get('admin/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('admin/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    });

    // Candidate Dashboard
    Route::middleware(['role:Candidate'])->group(function () {
        Route::get('/my-applications', [ApplicationController::class, 'myApplications'])->name('my-applications');
        Route::get('/my-profile', [ApplicationController::class, 'myProfile'])->name('my-profile');
        Route::post('/my-profile/update', [ApplicationController::class, 'updateProfile'])->name('my-profile.update');
    });
});

require __DIR__.'/settings.php';

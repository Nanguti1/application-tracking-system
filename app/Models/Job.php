<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Contracts\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Job extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'salary_min',
        'salary_max',
        'description',
        'requirements',
        'deadline_at',
        'interview_date_start',
        'interview_date_end',
        'candidates_to_shortlist',
        'interview_type',
        'status',
        'applications_count',
        'shortlisted_count',
        'rejected_count',
    ];

    protected $casts = [
        'deadline_at' => 'datetime',
        'interview_date_start' => 'datetime',
        'interview_date_end' => 'datetime',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'title', 'description', 'deadline_at']);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function interviewStages(): HasMany
    {
        return $this->hasMany(InterviewStage::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class);
    }

    public function shortlistedApplications()
    {
        return $this->hasMany(JobApplication::class)
            ->whereIn('status', ['shortlisted', 'interview', 'final_interview', 'offer', 'hired']);
    }

    public function rejectedApplications()
    {
        return $this->hasMany(JobApplication::class)
            ->where('status', 'rejected');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function canAcceptApplications(): bool
    {
        return $this->status === 'published' &&
               (! $this->deadline_at || $this->deadline_at->isFuture());
    }
}

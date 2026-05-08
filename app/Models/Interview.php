<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Interview extends Model
{
    use LogsActivity;

    protected $fillable = [
        'job_id',
        'job_application_id',
        'interview_stage_id',
        'scheduled_at',
        'duration_minutes',
        'interview_type',
        'interviewer_name',
        'interviewer_email',
        'location',
        'meeting_link',
        'status',
        'feedback',
        'interviewer_score',
        'rescheduled_at',
        'reschedule_reason',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'interviewer_score' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'scheduled_at', 'interviewer_score', 'feedback']);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function interviewStage(): BelongsTo
    {
        return $this->belongsTo(InterviewStage::class);
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at->isFuture();
    }

    public function canBeRescheduled(): bool
    {
        return in_array($this->status, ['scheduled', 'rescheduled']);
    }
}

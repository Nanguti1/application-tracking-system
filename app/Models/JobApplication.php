<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\ActivityLog\Traits\LogsActivity;
use Spatie\ActivityLog\LogOptions;

class JobApplication extends Model
{
    use LogsActivity;

    protected $fillable = [
        'job_id',
        'user_id',
        'full_name',
        'email',
        'phone',
        'cover_letter',
        'linkedin_profile',
        'portfolio_url',
        'years_of_experience',
        'education_level',
        'expected_salary',
        'location',
        'availability_date',
        'status',
        'match_score',
        'ranking_score',
        'match_details',
        'shortlisted_at',
        'rejected_at',
        'rejection_reason',
        'cv_parsed',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'ranking_score' => 'decimal:2',
        'match_details' => 'array',
        'shortlisted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'availability_date' => 'date',
        'cv_parsed' => 'boolean',
        'expected_salary' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'match_score', 'ranking_score', 'shortlisted_at', 'rejected_at']);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resume(): HasOne
    {
        return $this->hasOne(Resume::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function offer(): HasOne
    {
        return $this->hasOne(Offer::class);
    }

    public function isShortlisted(): bool
    {
        return in_array($this->status, ['shortlisted', 'interview', 'final_interview', 'offer', 'hired']);
    }

    public function canBeRescheduled(): bool
    {
        return $this->status === 'interview';
    }
}

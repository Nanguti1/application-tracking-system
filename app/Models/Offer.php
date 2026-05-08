<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Contracts\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Offer extends Model
{
    use LogsActivity;

    protected $fillable = [
        'job_application_id',
        'offered_salary',
        'position_title',
        'start_date',
        'offer_letter',
        'status',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'expires_at',
    ];

    protected $casts = [
        'offered_salary' => 'decimal:2',
        'start_date' => 'date',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'offered_salary', 'sent_at', 'accepted_at', 'rejected_at']);
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeAccepted(): bool
    {
        return $this->status === 'sent' && ! $this->isExpired();
    }
}

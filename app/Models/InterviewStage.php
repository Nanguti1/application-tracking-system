<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewStage extends Model
{
    protected $fillable = [
        'job_id',
        'name',
        'order',
        'description',
        'duration_minutes',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewSchedule extends Model
{
    protected $fillable = [
        'job_id',
        'interview_date',
        'start_time',
        'end_time',
        'breaks',
        'slot_duration_minutes',
    ];

    protected $casts = [
        'interview_date' => 'date',
        'breaks' => 'array',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function getAvailableSlots(): array
    {
        $slots = [];
        $start = strtotime($this->interview_date->format('Y-m-d') . ' ' . $this->start_time);
        $end = strtotime($this->interview_date->format('Y-m-d') . ' ' . $this->end_time);
        $slotDuration = $this->slot_duration_minutes * 60;

        $breaks = $this->breaks ?? [];

        while ($start + $slotDuration <= $end) {
            $slotStart = date('H:i', $start);
            $slotEnd = date('H:i', $start + $slotDuration);

            // Check if slot conflicts with breaks
            $inBreak = false;
            foreach ($breaks as $break) {
                $breakStart = strtotime($break['start']);
                $breakEnd = strtotime($break['end']);
                if ($start < $breakEnd && $start + $slotDuration > $breakStart) {
                    $inBreak = true;
                    break;
                }
            }

            if (!$inBreak) {
                $slots[] = [
                    'start' => $slotStart,
                    'end' => $slotEnd,
                    'timestamp' => $start,
                ];
            }

            $start += $slotDuration;
        }

        return $slots;
    }
}

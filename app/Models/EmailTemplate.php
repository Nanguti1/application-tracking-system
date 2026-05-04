<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $fillable = [
        'job_id',
        'name',
        'type',
        'subject',
        'body',
        'placeholders_available',
        'is_default',
    ];

    protected $casts = [
        'placeholders_available' => 'array',
        'is_default' => 'boolean',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class)->withDefault();
    }

    public static function getDefaultTemplates(): array
    {
        return [
            'rejection' => [
                'subject' => 'Application Update - {job_title}',
                'body' => 'Dear {candidate_name},<br><br>Thank you for your interest in the {job_title} position. After careful consideration, we regret to inform you that we have decided to move forward with other candidates whose experience more closely aligns with our current needs.<br><br>We appreciate your time and interest in our company.',
                'type' => 'rejection',
            ],
            'shortlist' => [
                'subject' => 'Exciting News! You\'ve been shortlisted for {job_title}',
                'body' => 'Dear {candidate_name},<br><br>Congratulations! We are pleased to inform you that you have been shortlisted for the {job_title} position. Your qualifications and experience impressed our team.<br><br>We would like to invite you for an interview on {interview_date} at {interview_time}.',
                'type' => 'shortlist',
            ],
            'interview_invitation' => [
                'subject' => 'Interview Invitation - {job_title} at {company_name}',
                'body' => 'Dear {candidate_name},<br><br>We would like to invite you for an interview for the {job_title} position. Please see the details below:<br><br>Date: {interview_date}<br>Time: {interview_time}<br>Duration: {interview_duration}<br><br>Looking forward to meeting you!',
                'type' => 'interview_invitation',
            ],
            'offer' => [
                'subject' => 'Job Offer - {job_title} at {company_name}',
                'body' => 'Dear {candidate_name},<br><br>We are delighted to offer you the position of {job_title}. Please find attached our formal offer letter with all relevant details.<br><br>We look forward to welcoming you to our team!',
                'type' => 'offer',
            ],
            'congratulations' => [
                'subject' => 'Welcome to {company_name}!',
                'body' => 'Dear {candidate_name},<br><br>Congratulations! You have been selected for the {job_title} position. We are excited to have you join our team starting {start_date}.<br><br>Welcome aboard!',
                'type' => 'congratulations',
            ],
        ];
    }
}

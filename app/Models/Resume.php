<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resume extends Model
{
    protected $fillable = [
        'job_application_id',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'raw_text',
        'parsed_data',
        'extracted_email',
        'extracted_phone',
    ];

    protected $casts = [
        'parsed_data' => 'array',
        'extracted_email' => 'array',
        'extracted_phone' => 'array',
    ];

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function getSkills(): array
    {
        return $this->parsed_data['skills'] ?? [];
    }

    public function getExperience(): array
    {
        return $this->parsed_data['experience'] ?? [];
    }

    public function getEducation(): array
    {
        return $this->parsed_data['education'] ?? [];
    }

    public function getCertifications(): array
    {
        return $this->parsed_data['certifications'] ?? [];
    }
}

<?php

namespace Database\Factories;

use App\Models\Resume;
use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResumeFactory extends Factory
{
    protected $model = Resume::class;

    public function definition(): array
    {
        return [
            'job_application_id' => JobApplication::factory(),
            'original_filename' => $this->faker->word() . '.pdf',
            'file_path' => '/resumes/' . $this->faker->uuid() . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(100000, 5000000),
            'raw_text' => $this->faker->paragraphs(5, true),
            'parsed_data' => [
                'skills' => ['PHP', 'Laravel', 'JavaScript', 'React', 'AWS'],
                'experience' => ['Software Developer - 5 years', 'Junior Developer - 2 years'],
                'education' => ['Bachelor of Science in Computer Science'],
                'certifications' => ['AWS Certified Solutions Architect'],
            ],
            'extracted_email' => ['john@example.com'],
            'extracted_phone' => ['+1 (555) 123-4567'],
        ];
    }
}

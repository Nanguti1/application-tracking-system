<?php

namespace Database\Factories;

use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'department' => $this->faker->word(),
            'location' => $this->faker->city() . ', ' . $this->faker->state(),
            'employment_type' => $this->faker->randomElement(['full_time', 'part_time', 'contract']),
            'salary_min' => $this->faker->numberBetween(50000, 100000),
            'salary_max' => $this->faker->numberBetween(100000, 200000),
            'description' => $this->faker->paragraphs(3, true),
            'requirements' => $this->faker->paragraphs(2, true),
            'deadline_at' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'candidates_to_shortlist' => 10,
            'interview_type' => 'fixed',
            'status' => 'published',
            'applications_count' => 0,
            'shortlisted_count' => 0,
            'rejected_count' => 0,
        ];
    }
}

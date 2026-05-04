<?php

namespace Database\Factories;

use App\Models\JobApplication;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'user_id' => User::factory(),
            'full_name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'cover_letter' => $this->faker->paragraphs(2, true),
            'linkedin_profile' => 'https://linkedin.com/in/' . $this->faker->slug(),
            'portfolio_url' => 'https://' . $this->faker->domainName(),
            'years_of_experience' => $this->faker->numberBetween(0, 20),
            'education_level' => $this->faker->randomElement(['High School', 'Bachelor', 'Master', 'PhD']),
            'expected_salary' => $this->faker->numberBetween(50000, 150000),
            'location' => $this->faker->city(),
            'availability_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => 'applied',
            'match_score' => $this->faker->randomFloat(2, 0, 100),
            'ranking_score' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}

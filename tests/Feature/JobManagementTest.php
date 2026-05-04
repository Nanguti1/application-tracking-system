<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class JobManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'HR Admin']);
        Role::create(['name' => 'Recruiter']);
    }

    public function test_hr_admin_can_create_job()
    {
        $user = User::factory()->create();
        $user->assignRole('HR Admin');

        $response = $this->actingAs($user)->post(route('jobs.store'), [
            'title' => 'Software Engineer',
            'department' => 'Engineering',
            'location' => 'San Francisco, CA',
            'employment_type' => 'full_time',
            'salary_min' => 100000,
            'salary_max' => 150000,
            'description' => 'We are looking for a talented software engineer...',
            'requirements' => 'Minimum 3 years of experience...',
            'candidates_to_shortlist' => 10,
            'interview_type' => 'fixed',
        ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Software Engineer',
            'department' => 'Engineering',
        ]);
    }

    public function test_job_can_be_published()
    {
        $user = User::factory()->create();
        $user->assignRole('HR Admin');

        $job = Job::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($user)->post(route('jobs.publish', $job));

        $this->assertEquals('published', $job->fresh()->status);
    }

    public function test_job_can_be_duplicated()
    {
        $user = User::factory()->create();
        $user->assignRole('HR Admin');

        $original = Job::factory()->create([
            'title' => 'Original Job',
            'department' => 'Engineering',
        ]);

        $response = $this->actingAs($user)->post(route('jobs.duplicate', $original));

        $this->assertDatabaseHas('jobs', ['title' => 'Original Job', 'status' => 'draft']);
        $this->assertEquals(2, Job::where('title', 'Original Job')->count());
    }

    public function test_recruiter_cannot_create_job()
    {
        $user = User::factory()->create();
        $user->assignRole('Recruiter');

        $response = $this->actingAs($user)->post(route('jobs.store'), [
            'title' => 'Software Engineer',
            'department' => 'Engineering',
            'location' => 'San Francisco, CA',
            'employment_type' => 'full_time',
            'description' => 'Description',
            'requirements' => 'Requirements',
            'candidates_to_shortlist' => 10,
            'interview_type' => 'fixed',
        ]);

        $response->assertForbidden();
    }

    public function test_public_can_view_published_jobs()
    {
        $job = Job::factory()->create(['status' => 'published']);

        $response = $this->get(route('careers.show', $job));

        $response->assertSuccessful();
        $response->assertInertia(fn ($page) => $page->component('Careers/Show'));
    }

    public function test_public_cannot_view_draft_jobs()
    {
        $job = Job::factory()->create(['status' => 'draft']);

        $response = $this->get(route('careers.show', $job));

        $response->assertNotFound();
    }
}

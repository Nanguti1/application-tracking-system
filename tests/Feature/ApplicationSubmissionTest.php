<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use App\Models\JobApplication;
use App\Services\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ApplicationSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_apply_for_job()
    {
        $candidate = User::factory()->create();
        $job = Job::factory()->create(['status' => 'published']);

        $response = $this->actingAs($candidate)->post(route('careers.apply', $job), [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
            'years_of_experience' => 5,
            'cover_letter' => 'I am very interested in this position...',
            'linkedin_profile' => 'https://linkedin.com/in/johndoe',
            'portfolio_url' => 'https://johndoe.dev',
            'location' => 'San Francisco, CA',
            'resume' => UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf'),
        ]);

        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'user_id' => $candidate->id,
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_candidate_cannot_apply_twice_for_same_job()
    {
        $candidate = User::factory()->create();
        $job = Job::factory()->create(['status' => 'published']);

        JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $candidate->id,
        ]);

        $this->actingAs($candidate)->expectException(\Exception::class);

        $applicationService = app(ApplicationService::class);
        $applicationService->submitApplication([
            'job_id' => $job->id,
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
            'years_of_experience' => 5,
        ]);
    }

    public function test_cannot_apply_to_closed_job()
    {
        $candidate = User::factory()->create();
        $job = Job::factory()->create(['status' => 'closed']);

        $response = $this->actingAs($candidate)->post(route('careers.apply', $job), [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
            'years_of_experience' => 5,
            'resume' => UploadedFile::fake()->create('resume.pdf', 200, 'application/pdf'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function test_application_status_can_be_updated()
    {
        $hr = User::factory()->create();
        $hr->givePermissionTo('update_application_status');

        $application = JobApplication::factory()->create();
        $applicationService = app(ApplicationService::class);

        $applicationService->updateApplicationStatus($application, 'shortlisted');

        $this->assertEquals('shortlisted', $application->fresh()->status);
    }

    public function test_applications_can_be_filtered_by_score()
    {
        $job = Job::factory()->create();
        
        JobApplication::factory(5)->create([
            'job_id' => $job->id,
            'match_score' => 45,
        ]);

        JobApplication::factory(3)->create([
            'job_id' => $job->id,
            'match_score' => 75,
        ]);

        $service = app(ApplicationService::class);
        $filtered = $service->filterApplications($job, ['min_score' => 70]);

        $this->assertEquals(3, $filtered->total());
    }

    public function test_candidate_can_withdraw_application()
    {
        $candidate = User::factory()->create();
        $application = JobApplication::factory()->create(['user_id' => $candidate->id]);

        $service = app(ApplicationService::class);
        $service->withdrawApplication($application);

        $this->assertEquals('rejected', $application->fresh()->status);
    }
}

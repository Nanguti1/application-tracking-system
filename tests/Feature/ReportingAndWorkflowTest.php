<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Notifications\ApplicationStatusChanged;
use App\Services\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportingAndWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'HR Admin']);
    }

    public function test_reports_page_includes_advanced_analytics_payload(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('HR Admin');

        $job = Job::factory()->create();

        JobApplication::factory()->count(2)->create(['job_id' => $job->id, 'status' => 'hired']);
        JobApplication::factory()->count(3)->create(['job_id' => $job->id, 'status' => 'rejected']);
        JobApplication::factory()->count(1)->create(['job_id' => $job->id, 'status' => 'shortlisted']);

        $response = $this->actingAs($admin)->get(route('reports.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Reports/Index')
            ->has('statusBreakdown')
            ->has('topJobs')
            ->has('applicationTrend', 7)
            ->where('totals.hired', 2)
            ->where('totals.rejected', 3)
            ->where('totals.inProcess', 1)
        );
    }

    public function test_status_update_queues_email_notification_and_uses_template(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $job = Job::factory()->create(['title' => 'Senior Engineer']);
        $application = JobApplication::factory()->create([
            'job_id' => $job->id,
            'user_id' => $user->id,
            'status' => 'applied',
            'full_name' => 'Ada Lovelace',
        ]);

        EmailTemplate::query()->create([
            'job_id' => $job->id,
            'name' => 'Shortlist Email',
            'type' => 'shortlist',
            'subject' => 'You are shortlisted for {job_title}',
            'body' => 'Hello {candidate_name}, role: {job_title}.',
            'is_default' => false,
        ]);

        $service = app(ApplicationService::class);
        $service->updateApplicationStatus($application, 'shortlisted');

        Notification::assertSentTo($user, ApplicationStatusChanged::class, function (ApplicationStatusChanged $notification, array $channels) use ($user): bool {
            $mail = $notification->toMail($user);

            return in_array('mail', $channels, true)
                && str_contains($mail->subject, 'Senior Engineer')
                && str_contains($mail->render(), 'Ada Lovelace');
        });
    }
}

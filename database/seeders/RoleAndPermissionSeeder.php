<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions
        $permissions = [
            // Jobs
            'create_jobs',
            'edit_jobs',
            'delete_jobs',
            'view_jobs',
            'publish_jobs',
            'archive_jobs',
            'duplicate_jobs',
            'view_job_analytics',

            // Applications
            'view_applications',
            'shortlist_applications',
            'reject_applications',
            'update_application_status',
            'export_applications',

            // Interviews
            'schedule_interviews',
            'reschedule_interviews',
            'cancel_interviews',
            'view_interviews',
            'submit_interview_feedback',

            // Email Templates
            'manage_email_templates',
            'view_email_templates',
            'send_emails',

            // Offers
            'create_offers',
            'send_offers',
            'view_offers',

            // Candidates
            'view_candidates',
            'export_candidates',
            'manage_candidate_profile',

            // Pipeline
            'view_pipeline',
            'manage_pipeline',

            // Settings
            'manage_settings',
            'view_reports',
            'manage_users',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $hrAdminRole = Role::firstOrCreate(['name' => 'HR Admin']);
        $recruiterRole = Role::firstOrCreate(['name' => 'Recruiter']);
        $interviewerRole = Role::firstOrCreate(['name' => 'Interviewer']);
        $candidateRole = Role::firstOrCreate(['name' => 'Candidate']);

        // Assign all permissions to Super Admin
        $superAdminRole->syncPermissions($permissions);

        // Assign permissions to HR Admin
        $hrAdminPermissions = [
            'create_jobs',
            'edit_jobs',
            'delete_jobs',
            'view_jobs',
            'publish_jobs',
            'archive_jobs',
            'duplicate_jobs',
            'view_job_analytics',
            'view_applications',
            'shortlist_applications',
            'reject_applications',
            'update_application_status',
            'export_applications',
            'schedule_interviews',
            'reschedule_interviews',
            'cancel_interviews',
            'view_interviews',
            'manage_email_templates',
            'view_email_templates',
            'send_emails',
            'create_offers',
            'send_offers',
            'view_offers',
            'view_candidates',
            'export_candidates',
            'view_pipeline',
            'manage_pipeline',
            'view_reports',
            'manage_users',
        ];
        $hrAdminRole->syncPermissions($hrAdminPermissions);

        // Assign permissions to Recruiter
        $recruiterPermissions = [
            'view_jobs',
            'view_applications',
            'shortlist_applications',
            'reject_applications',
            'update_application_status',
            'export_applications',
            'schedule_interviews',
            'reschedule_interviews',
            'view_interviews',
            'view_email_templates',
            'send_emails',
            'view_candidates',
            'export_candidates',
            'view_pipeline',
            'manage_pipeline',
            'view_reports',
        ];
        $recruiterRole->syncPermissions($recruiterPermissions);

        // Assign permissions to Interviewer
        $interviewerPermissions = [
            'view_applications',
            'view_interviews',
            'submit_interview_feedback',
            'view_candidates',
        ];
        $interviewerRole->syncPermissions($interviewerPermissions);

        // Assign permissions to Candidate (minimal)
        $candidatePermissions = [
            'manage_candidate_profile',
        ];
        $candidateRole->syncPermissions($candidatePermissions);
    }
}

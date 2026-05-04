<?php

namespace App\Notifications;

use App\Models\JobApplication;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ApplicationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private JobApplication $application,
        private string $oldStatus,
        private string $newStatus,
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $template = $this->getEmailTemplate();

        if (!$template) {
            return $this->defaultEmail($notifiable);
        }

        $body = $this->replacePlaceholders($template->body);
        $subject = $this->replacePlaceholders($template->subject);

        return (new MailMessage)
            ->subject($subject)
            ->html($body);
    }

    private function getEmailTemplate(): ?EmailTemplate
    {
        $job = $this->application->job;
        $type = $this->getTemplateType($this->newStatus);

        return EmailTemplate::where('job_id', $job->id)
            ->where('type', $type)
            ->first();
    }

    private function getTemplateType(string $status): ?string
    {
        return match ($status) {
            'rejected' => 'rejection',
            'shortlisted' => 'shortlist',
            'interview' => 'interview_invitation',
            'offer' => 'offer',
            'hired' => 'congratulations',
            default => null,
        };
    }

    private function replacePlaceholders(string $content): string
    {
        $replacements = [
            '{candidate_name}' => $this->application->full_name,
            '{job_title}' => $this->application->job->title,
            '{company_name}' => config('app.name'),
            '{interview_date}' => now()->addDays(7)->format('F j, Y'),
            '{interview_time}' => '10:00 AM',
            '{interview_duration}' => '60 minutes',
            '{start_date}' => now()->addDays(30)->format('F j, Y'),
        ];

        foreach ($replacements as $placeholder => $value) {
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }

    private function defaultEmail($notifiable): MailMessage
    {
        $message = match ($this->newStatus) {
            'shortlisted' => "Congratulations! Your application for {$this->application->job->title} has been shortlisted.",
            'rejected' => "Thank you for your interest. We regret to inform you that your application has not been selected.",
            'interview' => "You have been invited for an interview for the {$this->application->job->title} position.",
            'offer' => "We are pleased to offer you the position of {$this->application->job->title}.",
            'hired' => "Welcome to our team! We're excited to have you join us.",
            default => "Your application status has been updated to: {$this->newStatus}",
        };

        return (new MailMessage)
            ->subject("Application Update - {$this->application->job->title}")
            ->line($message)
            ->action('View Application', route('applications.show', $this->application))
            ->line('Thank you for your interest in joining us!');
    }
}

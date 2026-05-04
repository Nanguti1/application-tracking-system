<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Job;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplateController extends Controller
{
    public function index(Job $job): Response
    {
        $templates = EmailTemplate::where('job_id', $job->id)->orderBy('type')->get();

        return Inertia::render('Admin/EmailTemplates/Index', [
            'job' => $job,
            'templates' => $templates,
        ]);
    }

    public function edit(Job $job, EmailTemplate $template): Response
    {
        abort_if($template->job_id !== $job->id, 404);

        return Inertia::render('Admin/EmailTemplates/Edit', [
            'job' => $job,
            'template' => $template,
        ]);
    }

    public function update(Request $request, Job $job, EmailTemplate $template)
    {
        abort_if($template->job_id !== $job->id, 404);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $template->update($validated);

        return redirect()->route('email-templates.index', $job)->with('success', 'Template updated.');
    }
}

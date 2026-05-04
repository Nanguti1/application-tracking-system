import { Head, router } from '@inertiajs/react';
import JobForm from './JobForm';

type Job = Record<string, any>;

export default function EditJob({ job }: { job: Job }) {
    return (
        <>
            <Head title={`Edit ${job.title}`} />
            <div className="mx-auto max-w-3xl space-y-6">
                <h1 className="text-3xl font-bold">Edit Job</h1>
                <JobForm
                    initial={{
                        title: job.title,
                        department: job.department,
                        location: job.location,
                        employment_type: job.employment_type,
                        salary_min: job.salary_min ?? '',
                        salary_max: job.salary_max ?? '',
                        description: job.description,
                        requirements: job.requirements,
                        deadline_at: job.deadline_at ?? '',
                        interview_date_start: job.interview_date_start ?? '',
                        interview_date_end: job.interview_date_end ?? '',
                        candidates_to_shortlist: job.candidates_to_shortlist,
                        interview_type: job.interview_type,
                    }}
                    submitLabel="Update"
                    submit={(data) => router.put(route('jobs.update', job.id), data)}
                />
            </div>
        </>
    );
}

import { Head, router } from '@inertiajs/react';
import JobForm from './JobForm';

export default function CreateJob() {
    return (
        <>
            <Head title="Create Job" />
            <div className="mx-auto max-w-3xl space-y-6">
                <h1 className="text-3xl font-bold">Create Job</h1>
                <JobForm
                    initial={{
                        title: '',
                        department: '',
                        location: '',
                        employment_type: 'full_time',
                        salary_min: '',
                        salary_max: '',
                        description: '',
                        requirements: '',
                        deadline_at: '',
                        interview_date_start: '',
                        interview_date_end: '',
                        candidates_to_shortlist: 10,
                        interview_type: 'fixed',
                    }}
                    submitLabel="Create"
                    submit={(data) => router.post(route('jobs.store'), data)}
                />
            </div>
        </>
    );
}

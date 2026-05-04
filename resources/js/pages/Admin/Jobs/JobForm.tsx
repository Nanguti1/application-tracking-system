import { FormEventHandler } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type JobPayload = {
    title: string;
    department: string;
    location: string;
    employment_type: string;
    salary_min: string;
    salary_max: string;
    description: string;
    requirements: string;
    deadline_at: string;
    interview_date_start: string;
    interview_date_end: string;
    candidates_to_shortlist: number;
    interview_type: string;
};

export default function JobForm({
    initial,
    submitLabel,
    submit,
}: {
    initial: JobPayload;
    submitLabel: string;
    submit: (data: JobPayload) => void;
}) {
    const { data, setData, processing } = useForm<JobPayload>(initial);

    const onSubmit: FormEventHandler = (event) => {
        event.preventDefault();
        submit(data);
    };

    return (
        <form className="space-y-4" onSubmit={onSubmit}>
            <Input value={data.title} onChange={(event) => setData('title', event.target.value)} placeholder="Job title" required />
            <Input value={data.department} onChange={(event) => setData('department', event.target.value)} placeholder="Department" required />
            <Input value={data.location} onChange={(event) => setData('location', event.target.value)} placeholder="Location" required />
            <Input value={data.employment_type} onChange={(event) => setData('employment_type', event.target.value)} placeholder="full_time" required />
            <Input value={data.salary_min} onChange={(event) => setData('salary_min', event.target.value)} placeholder="Salary min" />
            <Input value={data.salary_max} onChange={(event) => setData('salary_max', event.target.value)} placeholder="Salary max" />
            <textarea className="w-full rounded border p-2" value={data.description} onChange={(event) => setData('description', event.target.value)} placeholder="Description" required />
            <textarea className="w-full rounded border p-2" value={data.requirements} onChange={(event) => setData('requirements', event.target.value)} placeholder="Requirements" required />
            <Input type="datetime-local" value={data.deadline_at} onChange={(event) => setData('deadline_at', event.target.value)} />
            <Input type="datetime-local" value={data.interview_date_start} onChange={(event) => setData('interview_date_start', event.target.value)} />
            <Input type="datetime-local" value={data.interview_date_end} onChange={(event) => setData('interview_date_end', event.target.value)} />
            <Input type="number" value={data.candidates_to_shortlist} onChange={(event) => setData('candidates_to_shortlist', Number(event.target.value))} min={1} required />
            <Input value={data.interview_type} onChange={(event) => setData('interview_type', event.target.value)} placeholder="fixed or rolling" required />
            <Button type="submit" disabled={processing}>{submitLabel}</Button>
        </form>
    );
}

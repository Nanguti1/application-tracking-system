import { Head, Link } from '@inertiajs/react';

export default function InterviewIndex({ interviews }: any) {
    return (
        <>
            <Head title="Interviews" />
            <div className="space-y-4">
                <h1 className="text-3xl font-bold">Interviews</h1>
                {interviews.data.map((interview: any) => (
                    <Link key={interview.id} className="block rounded border p-3" href={route('interviews.show', interview.id)}>
                        {interview.job_application?.full_name ?? 'Candidate'} - {interview.status}
                    </Link>
                ))}
            </div>
        </>
    );
}

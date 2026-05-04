import { Head } from '@inertiajs/react';

export default function InterviewShow({ interview }: any) {
    return (
        <>
            <Head title={`Interview #${interview.id}`} />
            <div className="space-y-3">
                <h1 className="text-3xl font-bold">Interview #{interview.id}</h1>
                <p>Status: {interview.status}</p>
                <p>Scheduled at: {interview.scheduled_at}</p>
                <p>Duration: {interview.duration_minutes} minutes</p>
            </div>
        </>
    );
}

import { Head, Link } from '@inertiajs/react';

export default function CandidateApplications({ applications }: any) {
    return (
        <>
            <Head title="My Applications" />
            <div className="space-y-4">
                <h1 className="text-3xl font-bold">My Applications</h1>
                {applications.data.map((application: any) => (
                    <div key={application.id} className="rounded border p-4">
                        <p className="font-semibold">{application.job.title}</p>
                        <p>Status: {application.status}</p>
                    </div>
                ))}
            </div>
        </>
    );
}

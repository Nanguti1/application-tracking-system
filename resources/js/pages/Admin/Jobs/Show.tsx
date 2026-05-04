import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';

export default function ShowJob({ job, applicationsCount, shortlistedCount, rejectedCount }: any) {
    return (
        <>
            <Head title={job.title} />
            <div className="space-y-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-3xl font-bold">{job.title}</h1>
                    <Link href={route('jobs.edit', job.id)}><Button>Edit</Button></Link>
                </div>
                <p><strong>Department:</strong> {job.department}</p>
                <p><strong>Location:</strong> {job.location}</p>
                <p><strong>Status:</strong> {job.status}</p>
                <p><strong>Applications:</strong> {applicationsCount} | <strong>Shortlisted:</strong> {shortlistedCount} | <strong>Rejected:</strong> {rejectedCount}</p>
                <div className="prose max-w-none">
                    <h2>Description</h2>
                    <p>{job.description}</p>
                    <h2>Requirements</h2>
                    <p>{job.requirements}</p>
                </div>
            </div>
        </>
    );
}

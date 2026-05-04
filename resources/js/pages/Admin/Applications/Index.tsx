import { Head, Link } from '@inertiajs/react';

export default function ApplicationsIndex({ job, applications }: any) {
    return (
        <>
            <Head title={`Applications - ${job.title}`} />
            <div className="space-y-4">
                <h1 className="text-3xl font-bold">Applications for {job.title}</h1>
                {applications.data.map((application: any) => (
                    <Link className="block rounded border p-4" key={application.id} href={route('applications.show', application.id)}>
                        <p className="font-semibold">{application.full_name}</p>
                        <p>{application.email} • {application.status}</p>
                    </Link>
                ))}
            </div>
        </>
    );
}

import { Head, router } from '@inertiajs/react';

const statuses = ['applied', 'screening', 'shortlisted', 'interview', 'final_interview', 'offer', 'hired', 'rejected'];

export default function Pipeline({ job, pipeline }: any) {
    return (
        <>
            <Head title={`Pipeline - ${job.title}`} />
            <div className="space-y-4">
                <h1 className="text-3xl font-bold">Pipeline for {job.title}</h1>
                <div className="grid grid-cols-1 gap-4 md:grid-cols-4">
                    {statuses.map((status) => (
                        <div key={status} className="rounded border p-3">
                            <h2 className="mb-2 font-semibold capitalize">{status.replace('_', ' ')}</h2>
                            {(pipeline[status] ?? []).map((application: any) => (
                                <div className="mb-2 rounded bg-gray-50 p-2" key={application.id}>
                                    <p className="text-sm font-medium">{application.full_name}</p>
                                </div>
                            ))}
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}

import { Head, router } from '@inertiajs/react';

const statuses = ['applied', 'screening', 'shortlisted', 'interview', 'final_interview', 'offer', 'hired', 'rejected'];

export default function Pipeline({ job, pipeline }: any) {
    const shiftStatus = (applicationId: number, status: string, direction: -1 | 1): void => {
        const index = statuses.indexOf(status);
        const target = statuses[index + direction];
        if (!target) {
            return;
        }
        router.post(route('pipeline.move', job.id), { application_id: applicationId, status: target });
    };

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
                                    <div className="mt-2 flex gap-2">
                                        <button className="rounded border px-2 py-1 text-xs" onClick={() => shiftStatus(application.id, status, -1)} type="button">Back</button>
                                        <button className="rounded border px-2 py-1 text-xs" onClick={() => shiftStatus(application.id, status, 1)} type="button">Next</button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ))}
                </div>
            </div>
        </>
    );
}

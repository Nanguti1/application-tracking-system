import { Head, Link } from '@inertiajs/react';

export default function ReportsIndex({ totals }: any) {
    return (
        <>
            <Head title="Reports" />
            <div className="space-y-4">
                <h1 className="text-3xl font-bold">Reports</h1>
                <div className="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div className="rounded border p-4">Applications: {totals.applications}</div>
                    <div className="rounded border p-4">Hired: {totals.hired}</div>
                    <div className="rounded border p-4">Rejected: {totals.rejected}</div>
                </div>
                <Link className="inline-block rounded bg-blue-600 px-4 py-2 text-white" href={route('reports.export.csv')}>Export CSV</Link>
            </div>
        </>
    );
}

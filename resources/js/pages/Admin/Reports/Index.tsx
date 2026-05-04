import { Head, Link } from '@inertiajs/react';

type ReportsProps = {
    totals: {
        applications: number;
        hired: number;
        rejected: number;
        inProcess: number;
        monthApplications: number;
    };
    statusBreakdown: Array<{ status: string; total: number }>;
    topJobs: Array<{ jobTitle: string; total: number; averageScore: number }>;
    applicationTrend: Array<{ date: string; label: string; total: number }>;
};

export default function ReportsIndex({ totals, statusBreakdown, topJobs, applicationTrend }: ReportsProps) {
    const maxTrendValue = Math.max(...applicationTrend.map((day) => day.total), 1);

    return (
        <>
            <Head title="Reports" />
            <div className="space-y-6">
                <h1 className="text-3xl font-bold">Advanced Analytics</h1>

                <div className="grid grid-cols-1 gap-3 md:grid-cols-5">
                    <div className="rounded border p-4">Applications: {totals.applications}</div>
                    <div className="rounded border p-4">Hired: {totals.hired}</div>
                    <div className="rounded border p-4">Rejected: {totals.rejected}</div>
                    <div className="rounded border p-4">In Process: {totals.inProcess}</div>
                    <div className="rounded border p-4">This Month: {totals.monthApplications}</div>
                </div>

                <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div className="rounded border p-4">
                        <h2 className="mb-3 text-lg font-semibold">Application Trend (7 days)</h2>
                        <div className="space-y-2">
                            {applicationTrend.map((day) => (
                                <div key={day.date} className="space-y-1">
                                    <div className="flex justify-between text-sm">
                                        <span>{day.label}</span>
                                        <span>{day.total}</span>
                                    </div>
                                    <div className="h-2 rounded bg-gray-100">
                                        <div className="h-2 rounded bg-blue-500" style={{ width: `${(day.total / maxTrendValue) * 100}%` }} />
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="rounded border p-4">
                        <h2 className="mb-3 text-lg font-semibold">Status Breakdown</h2>
                        <div className="space-y-2">
                            {statusBreakdown.map((item) => (
                                <div key={item.status} className="flex items-center justify-between rounded bg-gray-50 px-3 py-2 text-sm">
                                    <span className="capitalize">{item.status.replaceAll('_', ' ')}</span>
                                    <span className="font-semibold">{item.total}</span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                <div className="rounded border p-4">
                    <h2 className="mb-3 text-lg font-semibold">Top Jobs by Volume</h2>
                    <div className="overflow-x-auto">
                        <table className="min-w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="px-2 py-2">Job</th>
                                    <th className="px-2 py-2">Applications</th>
                                    <th className="px-2 py-2">Avg Match Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                {topJobs.map((job) => (
                                    <tr key={`${job.jobTitle}-${job.total}`} className="border-b">
                                        <td className="px-2 py-2">{job.jobTitle}</td>
                                        <td className="px-2 py-2">{job.total}</td>
                                        <td className="px-2 py-2">{job.averageScore}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                <Link className="inline-block rounded bg-blue-600 px-4 py-2 text-white" href={route('reports.export.csv')}>Export CSV</Link>
            </div>
        </>
    );
}

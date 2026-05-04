import React from 'react';
import { Head } from '@inertiajs/react';
import { Users, Briefcase, TrendingUp, CheckCircle } from 'lucide-react';

interface Stats {
    total_jobs: number;
    active_jobs: number;
    total_applicants: number;
    shortlisted: number;
    rejected: number;
    pending_interviews: number;
}

interface Props {
    stats: Stats;
    funnel: Record<string, number>;
    trends: Array<{ date: string; count: number }>;
    topJobs: Array<{ id: number; title: string; applications_count: number }>;
}

export default function Dashboard({ stats, funnel, trends, topJobs }: Props) {
    const StatCard = ({ label, value, icon: Icon }: any) => (
        <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center justify-between">
                <div>
                    <p className="text-gray-600 text-sm font-medium">{label}</p>
                    <p className="text-3xl font-bold text-gray-900 mt-2">{value}</p>
                </div>
                <Icon className="h-12 w-12 text-blue-100" />
            </div>
        </div>
    );

    return (
        <>
            <Head title="Dashboard" />

            <div className="space-y-8">
                {/* Header */}
                <div>
                    <h1 className="text-4xl font-bold text-gray-900">Dashboard</h1>
                    <p className="text-gray-600 mt-2">Overview of your recruitment pipeline</p>
                </div>

                {/* Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard label="Total Jobs" value={stats.total_jobs} icon={Briefcase} />
                    <StatCard label="Active Jobs" value={stats.active_jobs} icon={TrendingUp} />
                    <StatCard label="Total Applicants" value={stats.total_applicants} icon={Users} />
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <StatCard label="Shortlisted" value={stats.shortlisted} icon={CheckCircle} />
                    <StatCard label="Rejected" value={stats.rejected} icon={Users} />
                    <StatCard label="Pending Interviews" value={stats.pending_interviews} icon={Briefcase} />
                </div>

                {/* Hiring Funnel */}
                <div className="bg-white rounded-lg shadow p-6">
                    <h2 className="text-xl font-bold text-gray-900 mb-6">Hiring Funnel</h2>
                    <div className="space-y-4">
                        {Object.entries(funnel).map(([stage, count]) => {
                            const maxCount = Math.max(...Object.values(funnel) as number[]);
                            const percentage = (count / maxCount) * 100;

                            return (
                                <div key={stage}>
                                    <div className="flex justify-between items-center mb-2">
                                        <span className="text-sm font-medium text-gray-700">
                                            {stage.replace(/_/g, ' ').toUpperCase()}
                                        </span>
                                        <span className="text-sm font-semibold text-gray-900">{count}</span>
                                    </div>
                                    <div className="w-full bg-gray-200 rounded-full h-2">
                                        <div
                                            className="bg-blue-600 h-2 rounded-full"
                                            style={{ width: `${percentage}%` }}
                                        />
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>

                {/* Top Jobs */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div className="bg-white rounded-lg shadow p-6">
                        <h2 className="text-xl font-bold text-gray-900 mb-6">Top Jobs by Applications</h2>
                        <div className="space-y-4">
                            {topJobs.map((job) => (
                                <div key={job.id} className="flex items-center justify-between pb-4 border-b">
                                    <span className="text-gray-900 font-medium">{job.title}</span>
                                    <span className="text-gray-600 text-sm">{job.applications_count} applications</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Application Trends */}
                    <div className="bg-white rounded-lg shadow p-6">
                        <h2 className="text-xl font-bold text-gray-900 mb-6">Application Trends (30 days)</h2>
                        <div className="h-64 flex items-end gap-1">
                            {trends.map((trend) => {
                                const maxCount = Math.max(...trends.map(t => t.count), 1);
                                const height = (trend.count / maxCount) * 100;

                                return (
                                    <div
                                        key={trend.date}
                                        className="flex-1 bg-blue-600 rounded-t opacity-70 hover:opacity-100"
                                        style={{ height: `${height}%` }}
                                        title={`${trend.date}: ${trend.count} applications`}
                                    />
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

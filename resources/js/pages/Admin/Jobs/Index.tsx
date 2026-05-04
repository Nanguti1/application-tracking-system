import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { Plus, Edit, Archive, Eye } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

interface Job {
    id: number;
    title: string;
    department: string;
    location: string;
    status: string;
    applications_count: number;
    shortlisted_count: number;
    rejected_count: number;
    created_at: string;
}

interface Props {
    jobs: {
        data: Job[];
        links?: any;
    };
}

const statusColors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    published: 'bg-green-100 text-green-800',
    closed: 'bg-yellow-100 text-yellow-800',
    interviewing: 'bg-blue-100 text-blue-800',
    offer_stage: 'bg-purple-100 text-purple-800',
    filled: 'bg-indigo-100 text-indigo-800',
    archived: 'bg-red-100 text-red-800',
};

export default function JobsIndex({ jobs }: Props) {
    return (
        <>
            <Head title="Jobs" />

            <div className="space-y-8">
                {/* Header */}
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-4xl font-bold text-gray-900">Jobs</h1>
                        <p className="text-gray-600 mt-2">Manage job openings and track applications</p>
                    </div>
                    <Link href={route('jobs.create')} as="button">
                        <Button>
                            <Plus className="h-4 w-4 mr-2" />
                            Create Job
                        </Button>
                    </Link>
                </div>

                {/* Jobs Table */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                                    Job Title
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                                    Department
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                                    Location
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                                    Status
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                                    Applications
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-200">
                            {jobs.data.map((job) => (
                                <tr key={job.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="font-medium text-gray-900">{job.title}</div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {job.department}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {job.location}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <Badge className={statusColors[job.status] || 'bg-gray-100 text-gray-800'}>
                                            {job.status.replace('_', ' ')}
                                        </Badge>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="flex gap-2">
                                            <span className="text-sm text-gray-600">
                                                {job.applications_count} applied
                                            </span>
                                            <span className="text-sm text-green-600">
                                                {job.shortlisted_count} shortlisted
                                            </span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="flex gap-2">
                                            <Link href={route('jobs.show', job)}>
                                                <button className="p-2 text-blue-600 hover:bg-blue-50 rounded">
                                                    <Eye className="h-4 w-4" />
                                                </button>
                                            </Link>
                                            <Link href={route('jobs.edit', job)}>
                                                <button className="p-2 text-blue-600 hover:bg-blue-50 rounded">
                                                    <Edit className="h-4 w-4" />
                                                </button>
                                            </Link>
                                            <Link href={route('jobs.archive', job)} method="post" as="button">
                                                <button className="p-2 text-gray-600 hover:bg-gray-50 rounded">
                                                    <Archive className="h-4 w-4" />
                                                </button>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );
}

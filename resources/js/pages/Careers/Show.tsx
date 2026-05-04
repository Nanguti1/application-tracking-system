import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { MapPin, DollarSign, Calendar, FileText, AlertCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

interface Job {
    id: number;
    title: string;
    department: string;
    location: string;
    employment_type: string;
    salary_min?: number;
    salary_max?: number;
    description: string;
    requirements: string;
    deadline_at?: string;
}

interface Props {
    job: Job;
    userApplied: boolean;
    requiresAuth: boolean;
}

export default function CareersShow({ job, userApplied, requiresAuth }: Props) {
    const { data, setData, post, processing, errors } = useForm({
        full_name: '',
        email: '',
        phone: '',
        cover_letter: '',
        linkedin_profile: '',
        portfolio_url: '',
        years_of_experience: '0',
        education_level: '',
        expected_salary: '',
        location: '',
        availability_date: '',
        resume: null as File | null,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('careers.apply', job), { forceFormData: true });
    };

    if (userApplied) {
        return (
            <>
                <Head title={job.title} />
                <div className="min-h-screen bg-gray-50 py-12">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                            <AlertCircle className="h-12 w-12 text-blue-600 mx-auto mb-4" />
                            <h2 className="text-2xl font-bold text-blue-900 mb-2">
                                Already Applied
                            </h2>
                            <p className="text-blue-800 mb-4">
                                You have already applied for this position. Check your email for updates.
                            </p>
                            <Link href={route('my-applications')} as="button" className="text-blue-600 hover:text-blue-800">
                                View My Applications →
                            </Link>
                        </div>
                    </div>
                </div>
            </>
        );
    }

    return (
        <>
            <Head title={job.title} />

            <div className="min-h-screen bg-gray-50 py-12">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Job Header */}
                    <div className="bg-white rounded-lg shadow-sm p-8 mb-8">
                        <div className="mb-4">
                            <Link
                                href={route('careers.index')}
                                className="text-blue-600 hover:text-blue-700"
                            >
                                ← Back to Jobs
                            </Link>
                        </div>

                        <h1 className="text-4xl font-bold text-gray-900 mb-2">{job.title}</h1>
                        <p className="text-lg text-gray-600 mb-6">{job.department}</p>

                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                            <div className="flex items-center gap-2">
                                <MapPin className="h-5 w-5 text-gray-400" />
                                <span>{job.location}</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <FileText className="h-5 w-5 text-gray-400" />
                                <span>{job.employment_type.replace('_', ' ')}</span>
                            </div>
                            {(job.salary_min || job.salary_max) && (
                                <div className="flex items-center gap-2">
                                    <DollarSign className="h-5 w-5 text-gray-400" />
                                    <span>
                                        {job.salary_min && job.salary_max
                                            ? `$${job.salary_min.toLocaleString()} - $${job.salary_max.toLocaleString()}`
                                            : 'Competitive'}
                                    </span>
                                </div>
                            )}
                            {job.deadline_at && (
                                <div className="flex items-center gap-2">
                                    <Calendar className="h-5 w-5 text-gray-400" />
                                    <span>Deadline: {new Date(job.deadline_at).toLocaleDateString()}</span>
                                </div>
                            )}
                        </div>

                        <Button className="w-full sm:w-auto" size="lg">
                            Apply Now
                        </Button>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {/* Job Details */}
                        <div className="lg:col-span-2 space-y-8">
                            <div className="bg-white rounded-lg shadow-sm p-6">
                                <h2 className="text-2xl font-bold text-gray-900 mb-4">About This Role</h2>
                                <div
                                    className="prose max-w-none text-gray-700"
                                    dangerouslySetInnerHTML={{ __html: job.description }}
                                />
                            </div>

                            <div className="bg-white rounded-lg shadow-sm p-6">
                                <h2 className="text-2xl font-bold text-gray-900 mb-4">Requirements</h2>
                                <div
                                    className="prose max-w-none text-gray-700"
                                    dangerouslySetInnerHTML={{ __html: job.requirements }}
                                />
                            </div>

                            {/* Application Form */}
                            <div className="bg-white rounded-lg shadow-sm p-6">
                                <h2 className="text-2xl font-bold text-gray-900 mb-6">Apply For This Position</h2>

                                <form onSubmit={handleSubmit} className="space-y-6">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Full Name *
                                            </label>
                                            <Input
                                                type="text"
                                                value={data.full_name}
                                                onChange={(e) => setData('full_name', e.target.value)}
                                                placeholder="John Doe"
                                                className={errors.full_name ? 'border-red-500' : ''}
                                            />
                                            {errors.full_name && (
                                                <p className="text-red-600 text-sm mt-1">{errors.full_name}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Email *
                                            </label>
                                            <Input
                                                type="email"
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder="john@example.com"
                                                className={errors.email ? 'border-red-500' : ''}
                                            />
                                            {errors.email && (
                                                <p className="text-red-600 text-sm mt-1">{errors.email}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Phone *
                                            </label>
                                            <Input
                                                type="tel"
                                                value={data.phone}
                                                onChange={(e) => setData('phone', e.target.value)}
                                                placeholder="+1 (555) 000-0000"
                                                className={errors.phone ? 'border-red-500' : ''}
                                            />
                                            {errors.phone && (
                                                <p className="text-red-600 text-sm mt-1">{errors.phone}</p>
                                            )}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Years of Experience *
                                            </label>
                                            <Input
                                                type="number"
                                                value={data.years_of_experience}
                                                onChange={(e) => setData('years_of_experience', e.target.value)}
                                                min="0"
                                                className={errors.years_of_experience ? 'border-red-500' : ''}
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Education Level
                                            </label>
                                            <Input
                                                type="text"
                                                value={data.education_level}
                                                onChange={(e) => setData('education_level', e.target.value)}
                                                placeholder="Bachelor's, Master's, etc."
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Location
                                            </label>
                                            <Input
                                                type="text"
                                                value={data.location}
                                                onChange={(e) => setData('location', e.target.value)}
                                                placeholder="City, Country"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Expected Salary
                                            </label>
                                            <Input
                                                type="number"
                                                value={data.expected_salary}
                                                onChange={(e) => setData('expected_salary', e.target.value)}
                                                placeholder="$0"
                                                min="0"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-900 mb-2">
                                                Availability Date
                                            </label>
                                            <Input
                                                type="date"
                                                value={data.availability_date}
                                                onChange={(e) => setData('availability_date', e.target.value)}
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-900 mb-2">
                                            LinkedIn Profile
                                        </label>
                                        <Input
                                            type="url"
                                            value={data.linkedin_profile}
                                            onChange={(e) => setData('linkedin_profile', e.target.value)}
                                            placeholder="https://linkedin.com/in/..."
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-900 mb-2">
                                            Portfolio URL
                                        </label>
                                        <Input
                                            type="url"
                                            value={data.portfolio_url}
                                            onChange={(e) => setData('portfolio_url', e.target.value)}
                                            placeholder="https://portfolio.com"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-900 mb-2">
                                            Cover Letter
                                        </label>
                                        <Textarea
                                            value={data.cover_letter}
                                            onChange={(e) => setData('cover_letter', e.target.value)}
                                            placeholder="Tell us why you're interested in this position..."
                                            rows={6}
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-900 mb-2">
                                            CV / Resume (PDF or DOCX) *
                                        </label>
                                        <Input
                                            type="file"
                                            accept=".pdf,.doc,.docx"
                                            onChange={(e) => setData('resume', e.target.files?.[0] ?? null)}
                                        />
                                        {errors.resume && <p className="mt-1 text-sm text-red-600">{errors.resume}</p>}
                                    </div>

                                    <Button type="submit" disabled={processing} className="w-full">
                                        {processing ? 'Submitting...' : 'Submit Application'}
                                    </Button>
                                </form>
                            </div>
                        </div>

                        {/* Sidebar */}
                        <div className="lg:col-span-1">
                            <div className="bg-white rounded-lg shadow-sm p-6 sticky top-20">
                                <h3 className="text-lg font-bold text-gray-900 mb-4">Quick Info</h3>
                                <dl className="space-y-4">
                                    <div>
                                        <dt className="text-sm font-medium text-gray-600">Employment Type</dt>
                                        <dd className="text-gray-900">{job.employment_type.replace('_', ' ')}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-600">Department</dt>
                                        <dd className="text-gray-900">{job.department}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm font-medium text-gray-600">Location</dt>
                                        <dd className="text-gray-900">{job.location}</dd>
                                    </div>
                                    {job.deadline_at && (
                                        <div>
                                            <dt className="text-sm font-medium text-gray-600">Application Deadline</dt>
                                            <dd className="text-gray-900">{new Date(job.deadline_at).toLocaleDateString()}</dd>
                                        </div>
                                    )}
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

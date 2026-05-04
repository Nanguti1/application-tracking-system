import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import { Search, MapPin, Briefcase, DollarSign } from 'lucide-react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
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
}

interface Props {
    jobs: {
        data: Job[];
        links?: any;
    };
    search?: string;
    filters?: Record<string, any>;
    departments?: string[];
    locations?: string[];
}

export default function CareersIndex({ jobs, search, filters, departments, locations }: Props) {
    const [searchQuery, setSearchQuery] = useState(search || '');
    const [selectedDepartment, setSelectedDepartment] = useState(filters?.department || '');
    const [selectedLocation, setSelectedLocation] = useState(filters?.location || '');
    const [selectedType, setSelectedType] = useState(filters?.employment_type || '');

    const handleSearch = (e: React.FormEvent) => {
        e.preventDefault();
        // Submit search form
    };

    const formatSalary = (min?: number, max?: number) => {
        if (!min && !max) return 'Competitive';
        if (min && max) return `$${min.toLocaleString()} - $${max.toLocaleString()}`;
        if (min) return `From $${min.toLocaleString()}`;
        return `Up to $${max?.toLocaleString()}`;
    };

    return (
        <>
            <Head title="Careers" />

            <div className="min-h-screen bg-gray-50">
                {/* Hero Section */}
                <div className="bg-white border-b">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                        <h1 className="text-4xl font-bold text-gray-900 mb-4">Join Our Team</h1>
                        <p className="text-lg text-gray-600">Find your next opportunity with us</p>
                    </div>
                </div>

                {/* Search and Filters */}
                <div className="bg-white border-b sticky top-0 z-10">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <form onSubmit={handleSearch} className="space-y-4">
                            <div className="flex gap-4">
                                <div className="flex-1">
                                    <div className="relative">
                                        <Search className="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                                        <Input
                                            type="text"
                                            placeholder="Search jobs..."
                                            value={searchQuery}
                                            onChange={(e) => setSearchQuery(e.target.value)}
                                            className="pl-10"
                                        />
                                    </div>
                                </div>
                                <Button type="submit">Search</Button>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <Select value={selectedDepartment} onValueChange={setSelectedDepartment}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Department" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Departments</SelectItem>
                                        {departments?.map((dept) => (
                                            <SelectItem key={dept} value={dept}>
                                                {dept}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                <Select value={selectedLocation} onValueChange={setSelectedLocation}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Location" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Locations</SelectItem>
                                        {locations?.map((loc) => (
                                            <SelectItem key={loc} value={loc}>
                                                {loc}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                <Select value={selectedType} onValueChange={setSelectedType}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Employment Type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Types</SelectItem>
                                        <SelectItem value="full_time">Full Time</SelectItem>
                                        <SelectItem value="part_time">Part Time</SelectItem>
                                        <SelectItem value="contract">Contract</SelectItem>
                                        <SelectItem value="temporary">Temporary</SelectItem>
                                        <SelectItem value="internship">Internship</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </form>
                    </div>
                </div>

                {/* Jobs List */}
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    {jobs.data.length === 0 ? (
                        <div className="text-center py-12">
                            <Briefcase className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                            <h3 className="text-lg font-medium text-gray-900 mb-2">No jobs found</h3>
                            <p className="text-gray-600">Try adjusting your search or filters</p>
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {jobs.data.map((job) => (
                                <Link
                                    key={job.id}
                                    href={route('careers.show', job)}
                                    className="block p-6 bg-white border rounded-lg hover:shadow-md transition-shadow"
                                >
                                    <div className="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 className="text-xl font-semibold text-gray-900">{job.title}</h3>
                                            <p className="text-gray-600">{job.department}</p>
                                        </div>
                                        <span className="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">
                                            {job.employment_type.replace('_', ' ')}
                                        </span>
                                    </div>

                                    <div className="flex flex-wrap gap-4 text-sm text-gray-600">
                                        <div className="flex items-center gap-1">
                                            <MapPin className="h-4 w-4" />
                                            {job.location}
                                        </div>
                                        {(job.salary_min || job.salary_max) && (
                                            <div className="flex items-center gap-1">
                                                <DollarSign className="h-4 w-4" />
                                                {formatSalary(job.salary_min, job.salary_max)}
                                            </div>
                                        )}
                                    </div>

                                    <p className="text-gray-700 mt-4 line-clamp-2">{job.description}</p>
                                </Link>
                            ))}
                        </div>
                    )}

                    {/* Pagination */}
                    {jobs.links && (
                        <div className="mt-8 flex justify-center gap-2">
                            {/* Add pagination buttons */}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}

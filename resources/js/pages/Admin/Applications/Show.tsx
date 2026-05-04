import { Head } from '@inertiajs/react';

export default function ApplicationShow({ application }: any) {
    return (
        <>
            <Head title={`Application ${application.full_name}`} />
            <div className="space-y-3">
                <h1 className="text-3xl font-bold">{application.full_name}</h1>
                <p>{application.email}</p>
                <p>Status: {application.status}</p>
                <p>Match score: {application.match_score}</p>
                <p>Ranking score: {application.ranking_score}</p>
            </div>
        </>
    );
}

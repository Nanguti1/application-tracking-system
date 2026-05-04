import { Head, useForm } from '@inertiajs/react';

export default function CandidateProfile({ user }: any) {
    const { data, setData, post, processing } = useForm({
        name: user.name ?? '',
        email: user.email ?? '',
        phone: user.phone ?? '',
        location: user.location ?? '',
    });

    return (
        <>
            <Head title="My Profile" />
            <form className="mx-auto max-w-xl space-y-3" onSubmit={(e) => { e.preventDefault(); post(route('my-profile.update')); }}>
                <h1 className="text-3xl font-bold">My Profile</h1>
                <input className="w-full rounded border p-2" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                <input className="w-full rounded border p-2" value={data.email} onChange={(e) => setData('email', e.target.value)} />
                <input className="w-full rounded border p-2" value={data.phone} onChange={(e) => setData('phone', e.target.value)} />
                <input className="w-full rounded border p-2" value={data.location} onChange={(e) => setData('location', e.target.value)} />
                <button disabled={processing} className="rounded bg-black px-4 py-2 text-white" type="submit">Save</button>
            </form>
        </>
    );
}

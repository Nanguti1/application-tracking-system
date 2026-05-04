import { Head, useForm } from '@inertiajs/react';

export default function TemplateEdit({ job, template }: any) {
    const { data, setData, put, processing } = useForm({
        subject: template.subject,
        body: template.body,
    });

    return (
        <>
            <Head title={`Edit ${template.type}`} />
            <form className="mx-auto max-w-3xl space-y-3" onSubmit={(e) => { e.preventDefault(); put(route('email-templates.update', [job.id, template.id])); }}>
                <h1 className="text-3xl font-bold">Edit Template: {template.type}</h1>
                <input className="w-full rounded border p-2" value={data.subject} onChange={(e) => setData('subject', e.target.value)} />
                <textarea className="min-h-80 w-full rounded border p-2" value={data.body} onChange={(e) => setData('body', e.target.value)} />
                <button className="rounded bg-black px-4 py-2 text-white" disabled={processing} type="submit">Save</button>
            </form>
        </>
    );
}

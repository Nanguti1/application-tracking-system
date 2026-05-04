import { Head, useForm } from '@inertiajs/react';

const PLACEHOLDERS = [
    '{candidate_name}',
    '{job_title}',
    '{company_name}',
    '{interview_date}',
    '{interview_time}',
    '{interview_duration}',
    '{start_date}',
];

export default function TemplateEdit({ job, template }: any) {
    const { data, setData, put, processing } = useForm({
        subject: template.subject,
        body: template.body,
    });

    const insertPlaceholder = (placeholder: string): void => {
        setData('body', `${data.body}${data.body.endsWith(' ') || data.body.length === 0 ? '' : ' '}${placeholder}`);
    };

    return (
        <>
            <Head title={`Edit ${template.type}`} />
            <form className="mx-auto max-w-5xl space-y-4" onSubmit={(e) => { e.preventDefault(); put(route('email-templates.update', [job.id, template.id])); }}>
                <h1 className="text-3xl font-bold">Edit Template: {template.type}</h1>
                <input className="w-full rounded border p-2" value={data.subject} onChange={(e) => setData('subject', e.target.value)} />

                <div className="flex flex-wrap gap-2 rounded border p-2">
                    {PLACEHOLDERS.map((placeholder) => (
                        <button key={placeholder} className="rounded border px-2 py-1 text-xs" type="button" onClick={() => insertPlaceholder(placeholder)}>
                            {placeholder}
                        </button>
                    ))}
                </div>

                <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div>
                        <p className="mb-1 text-sm font-medium">HTML Template</p>
                        <textarea className="min-h-96 w-full rounded border p-2 font-mono" value={data.body} onChange={(e) => setData('body', e.target.value)} />
                    </div>
                    <div>
                        <p className="mb-1 text-sm font-medium">Live Preview</p>
                        <div className="min-h-96 rounded border bg-white p-4" dangerouslySetInnerHTML={{ __html: data.body }} />
                    </div>
                </div>

                <button className="rounded bg-black px-4 py-2 text-white" disabled={processing} type="submit">Save</button>
            </form>
        </>
    );
}

import { Head, Link } from '@inertiajs/react';

export default function TemplateIndex({ job, templates }: any) {
    return (
        <>
            <Head title={`Email Templates - ${job.title}`} />
            <div className="space-y-4">
                <h1 className="text-3xl font-bold">Email Templates: {job.title}</h1>
                {templates.map((template: any) => (
                    <div key={template.id} className="rounded border p-3">
                        <p className="font-semibold">{template.type}</p>
                        <p>{template.subject}</p>
                        <Link className="text-blue-600" href={route('email-templates.edit', [job.id, template.id])}>Edit</Link>
                    </div>
                ))}
            </div>
        </>
    );
}

import { Form, Link, usePage } from '@inertiajs/react';
import { dashboard } from '@/routes';
import tasks from '@/routes/tasks';
import projects from '@/routes/projects';
import { store as syncStore } from '@/routes/sync';
import type { ReactNode } from 'react';

type Props = {
    children: ReactNode;
};

const NAV_LINKS = [
    { label: 'Dashboard', route: dashboard },
    { label: 'Tasks', route: tasks.index },
    { label: 'Projects', route: projects.index },
];

export default function AppLayout({ children }: Props) {
    const { url } = usePage();

    return (
        <div className="flex min-h-screen bg-gray-50 dark:bg-gray-950">
            {/* Sidebar */}
            <aside className="flex w-56 shrink-0 flex-col border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div className="flex h-14 items-center px-4">
                    <span className="text-lg font-semibold text-gray-900 dark:text-white">
                        TickTick
                    </span>
                </div>

                <nav className="flex flex-1 flex-col gap-1 px-2 py-2">
                    {NAV_LINKS.map(({ label, route }) => {
                        const href = route().url;
                        const isActive = url === href || (href !== '/' && url.startsWith(href));

                        return (
                            <Link
                                key={label}
                                href={href}
                                className={`rounded-md px-3 py-2 text-sm font-medium transition-colors ${
                                    isActive
                                        ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white'
                                }`}
                            >
                                {label}
                            </Link>
                        );
                    })}
                </nav>

                <div className="border-t border-gray-200 p-3 dark:border-gray-800">
                    <Form action={syncStore().url} method="post">
                        {({ processing }) => (
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 disabled:opacity-60 dark:bg-blue-500 dark:hover:bg-blue-600"
                            >
                                {processing ? 'Syncing…' : 'Sync Now'}
                            </button>
                        )}
                    </Form>
                </div>
            </aside>

            {/* Main content */}
            <main className="flex-1 overflow-auto">
                {children}
            </main>
        </div>
    );
}

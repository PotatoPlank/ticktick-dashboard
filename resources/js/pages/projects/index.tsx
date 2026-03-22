import AppLayout from '@/components/layout/AppLayout';
import type { TickTickProject } from '@/types';
import { Head, Link } from '@inertiajs/react';
import taskRoutes from '@/routes/tasks';

type Props = {
    projects: TickTickProject[];
};

export default function ProjectsIndex({ projects }: Props) {
    return (
        <AppLayout>
            <Head title="Projects" />

            <div className="p-6">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">Projects</h1>

                {projects.length === 0 ? (
                    <p className="mt-6 text-sm text-gray-400 dark:text-gray-500">
                        No projects synced yet. Hit <strong>Sync Now</strong> to fetch your data.
                    </p>
                ) : (
                    <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {projects.map((project) => (
                            <Link
                                key={project.id}
                                href={taskRoutes.index({ query: { project_id: String(project.id) } }).url}
                                className="group flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-5 transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
                            >
                                <div className="flex items-center gap-2">
                                    <span
                                        className="h-3 w-3 rounded-full"
                                        style={{ backgroundColor: project.color ?? '#6b7280' }}
                                    />
                                    <span className="font-medium text-gray-900 group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">
                                        {project.name}
                                    </span>
                                </div>

                                <div className="flex gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>
                                        <strong className="font-semibold text-gray-900 dark:text-white">
                                            {project.active_tasks_count ?? 0}
                                        </strong>{' '}
                                        active
                                    </span>
                                    <span>
                                        <strong className="font-semibold text-gray-900 dark:text-white">
                                            {project.total_tasks_count ?? 0}
                                        </strong>{' '}
                                        total
                                    </span>
                                </div>
                            </Link>
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

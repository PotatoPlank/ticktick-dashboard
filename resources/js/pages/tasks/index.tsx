import AppLayout from '@/components/layout/AppLayout';
import PriorityBadge from '@/components/PriorityBadge';
import ProjectPill from '@/components/ProjectPill';
import type { PaginatedData, TickTickProject, TickTickTask } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import taskRoutes from '@/routes/tasks';
import { useCallback } from 'react';

type Filters = {
    status?: string;
    project_id?: string;
    priority?: string;
    search?: string;
};

type Props = {
    tasks: PaginatedData<TickTickTask>;
    projects: Pick<TickTickProject, 'id' | 'name' | 'color'>[];
    filters: Filters;
};

const PRIORITY_OPTIONS = [
    { value: '', label: 'All Priorities' },
    { value: '5', label: 'High' },
    { value: '3', label: 'Medium' },
    { value: '1', label: 'Low' },
    { value: '0', label: 'None' },
];

const STATUS_OPTIONS = [
    { value: '', label: 'Active' },
    { value: '2', label: 'Completed' },
];

export default function TasksIndex({ tasks, projects, filters }: Props) {
    const applyFilter = useCallback(
        (key: keyof Filters, value: string) => {
            router.get(taskRoutes.index().url, { ...filters, [key]: value || undefined }, {
                preserveState: true,
                replace: true,
            });
        },
        [filters],
    );

    return (
        <AppLayout>
            <Head title="Tasks" />

            <div className="p-6">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">Tasks</h1>

                {/* Filters */}
                <div className="mt-4 flex flex-wrap gap-3">
                    <input
                        type="search"
                        placeholder="Search tasks…"
                        defaultValue={filters.search}
                        onChange={(e) => applyFilter('search', e.target.value)}
                        className="rounded-md border border-gray-300 px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    />

                    <select
                        value={filters.status ?? ''}
                        onChange={(e) => applyFilter('status', e.target.value)}
                        className="rounded-md border border-gray-300 px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                        {STATUS_OPTIONS.map((o) => (
                            <option key={o.value} value={o.value}>
                                {o.label}
                            </option>
                        ))}
                    </select>

                    <select
                        value={filters.project_id ?? ''}
                        onChange={(e) => applyFilter('project_id', e.target.value)}
                        className="rounded-md border border-gray-300 px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="">All Projects</option>
                        {projects.map((p) => (
                            <option key={p.id} value={String(p.id)}>
                                {p.name}
                            </option>
                        ))}
                    </select>

                    <select
                        value={filters.priority ?? ''}
                        onChange={(e) => applyFilter('priority', e.target.value)}
                        className="rounded-md border border-gray-300 px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                        {PRIORITY_OPTIONS.map((o) => (
                            <option key={o.value} value={o.value}>
                                {o.label}
                            </option>
                        ))}
                    </select>
                </div>

                {/* Task list */}
                <div className="mt-4 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                    {tasks.data.length === 0 ? (
                        <p className="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                            No tasks found.
                        </p>
                    ) : (
                        <div className="divide-y divide-gray-100 dark:divide-gray-700">
                            {tasks.data.map((task) => (
                                <div
                                    key={task.id}
                                    className="flex items-start justify-between gap-4 px-4 py-3"
                                >
                                    <div className="flex min-w-0 flex-1 flex-col gap-1">
                                        <span className="truncate text-sm font-medium text-gray-900 dark:text-white">
                                            {task.title}
                                        </span>
                                        <div className="flex flex-wrap items-center gap-1.5">
                                            <PriorityBadge priority={task.priority} />
                                            <ProjectPill project={task.project} />
                                        </div>
                                    </div>
                                    {task.due_date && (
                                        <span className="shrink-0 text-xs text-gray-400">
                                            {new Date(task.due_date).toLocaleDateString(undefined, {
                                                month: 'short',
                                                day: 'numeric',
                                            })}
                                        </span>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>

                {/* Pagination */}
                {tasks.last_page > 1 && (
                    <div className="mt-4 flex flex-wrap gap-1">
                        {tasks.links.map((link, i) => (
                            link.url ? (
                                <Link
                                    key={i}
                                    href={link.url}
                                    className={`rounded px-3 py-1.5 text-sm ${
                                        link.active
                                            ? 'bg-blue-600 text-white'
                                            : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700'
                                    }`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ) : (
                                <span
                                    key={i}
                                    className="rounded px-3 py-1.5 text-sm text-gray-300 dark:text-gray-600"
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            )
                        ))}
                    </div>
                )}

                <p className="mt-2 text-xs text-gray-400">
                    {tasks.total} task{tasks.total !== 1 ? 's' : ''}
                </p>
            </div>
        </AppLayout>
    );
}

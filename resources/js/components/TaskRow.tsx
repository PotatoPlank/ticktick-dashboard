import type { TickTickTask } from '@/types';
import PriorityBadge from './PriorityBadge';
import ProjectPill from './ProjectPill';

type Props = {
    task: TickTickTask;
};

function formatDueDate(dateStr: string | null): string | null {
    if (!dateStr) return null;
    return new Date(dateStr).toLocaleDateString(undefined, { month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric' });
}

export default function TaskRow({ task }: Props) {
    const isOverdue =
        task.due_date && task.status === 0 && new Date(task.due_date) < new Date(new Date().toDateString());

    return (
        <div className="flex items-start justify-between gap-4 px-4 py-3">
            <div className="flex min-w-0 flex-1 flex-col gap-1">
                <span className="truncate text-sm font-medium text-gray-900 dark:text-white">
                    {task.title}
                </span>
                <div className="flex flex-wrap items-center gap-1.5">
                    <PriorityBadge priority={task.priority} />
                    <ProjectPill project={task.project} />
                    {task.tags?.map((tag) => (
                        <span
                            key={tag}
                            className="rounded px-1.5 py-0.5 text-xs text-gray-500 ring-1 ring-gray-300 dark:text-gray-400 dark:ring-gray-600"
                        >
                            #{tag}
                        </span>
                    ))}
                </div>
            </div>
            {task.due_date && (
                <span
                    className={`shrink-0 text-xs font-medium ${
                        isOverdue
                            ? 'text-red-600 dark:text-red-400'
                            : 'text-gray-500 dark:text-gray-400'
                    }`}
                >
                    {formatDueDate(task.due_date)}
                </span>
            )}
        </div>
    );
}

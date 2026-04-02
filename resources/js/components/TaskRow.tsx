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
    const taskDueDate = task.due_date ? new Date(task.due_date) : null;
    let habitDueDate = null;
    const now = new Date();
    if (task?.repeat_rule) {
        task.tags = ['Habit'];
        task.remindAt.forEach((reminder) => {
            if (habitDueDate){
                return;
            }
            const reminderDate = new Date(reminder);
            if(reminderDate <= now){
                habitDueDate = reminderDate;
            }
        });
    }
    const isOverdue =
        taskDueDate &&
        task.status === 0 &&
        taskDueDate < new Date(new Date().toDateString());
    const isSameHour =
        taskDueDate && now.getHours() === taskDueDate.getHours();
    const isFuture = taskDueDate && taskDueDate > now;
    const isHabit = task?.remindAt !== null;

    let timeColor = 'text-gray-500 dark:text-gray-400';
    let color = 'text-gray-900 dark:text-white';
    let bg = '';
        if (!task.is_all_day) {
            if (isOverdue) {
                timeColor = 'text-red-600 dark:text-red-400';
                color = 'text-red-600 dark:text-red-400';
            } else if (isFuture) {
                timeColor = 'text-blue-300 dark:text-blue-100';
                color = 'text-blue-300 dark:text-blue-100';
                bg = 'opacity-50';
            } else if (!isSameHour) {
                bg = 'opacity-90';
                color = 'text-red-300 dark:text-red-200';
                timeColor = 'text-red-300 dark:text-red-200';
            }
        }


    return (
        <div
            className={`flex items-start justify-between gap-4 px-4 py-3 ${bg}`}
        >
            <div className="flex min-w-0 flex-1 flex-col gap-1">
                <span
                    className={`truncate ${isHabit ? 'text-xs' : 'text-sm'} font-medium ${color}`}
                >
                    {task.title || task?.name || 'Untitled'}
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
            {taskDueDate && (
                <span className={`shrink-0 text-xs font-medium ${timeColor}`}>
                    {task.is_all_day ? 'All Day' : formatDueDate(task.due_date)}
                </span>
            )}
            {task?.reminders && (
                <span className={`shrink-0 text-xs font-medium ${timeColor}`}>
                    {!habitDueDate ? 'All Day' : formatDueDate(habitDueDate)}
                </span>
            )}
        </div>
    );
}

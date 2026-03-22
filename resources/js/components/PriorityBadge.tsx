type Props = {
    priority: 0 | 1 | 3 | 5;
};

const PRIORITY_LABELS: Record<number, string> = {
    0: 'None',
    1: 'Low',
    3: 'Medium',
    5: 'High',
};

const PRIORITY_CLASSES: Record<number, string> = {
    0: 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
    1: 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    3: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
    5: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
};

export default function PriorityBadge({ priority }: Props) {
    if (priority === 0) {
        return null;
    }

    return (
        <span className={`inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium ${PRIORITY_CLASSES[priority]}`}>
            {PRIORITY_LABELS[priority]}
        </span>
    );
}

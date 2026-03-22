type Props = {
    label: string;
    value: number;
    accent?: 'default' | 'warning' | 'danger' | 'success';
};

const ACCENT_CLASSES: Record<string, string> = {
    default: 'text-gray-900 dark:text-white',
    warning: 'text-yellow-600 dark:text-yellow-400',
    danger: 'text-red-600 dark:text-red-400',
    success: 'text-green-600 dark:text-green-400',
};

export default function StatsCard({ label, value, accent = 'default' }: Props) {
    return (
        <div className="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <p className="text-sm font-medium text-gray-500 dark:text-gray-400">{label}</p>
            <p className={`mt-2 text-3xl font-semibold tabular-nums ${ACCENT_CLASSES[accent]}`}>
                {value}
            </p>
        </div>
    );
}

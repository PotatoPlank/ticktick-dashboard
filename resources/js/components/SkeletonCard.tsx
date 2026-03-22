export default function SkeletonCard() {
    return (
        <div className="animate-pulse rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <div className="h-3 w-24 rounded bg-gray-200 dark:bg-gray-700" />
            <div className="mt-3 h-8 w-16 rounded bg-gray-200 dark:bg-gray-700" />
        </div>
    );
}

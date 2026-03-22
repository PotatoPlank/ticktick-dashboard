import AppLayout from '@/components/layout/AppLayout';
import SkeletonCard from '@/components/SkeletonCard';
import StatsCard from '@/components/StatsCard';
import TaskRow from '@/components/TaskRow';
import type { DashboardStats, TickTickTask } from '@/types';
import { Head, router } from '@inertiajs/react';
import Box from '@mui/material/Box';
import Tabs from '@mui/material/Tabs';
import Tab from '@mui/material/Tab';
import TabContext from '@mui/lab/TabContext';
import TabList from '@mui/lab/TabList';
import TabPanel from '@mui/lab/TabPanel';
import { useEffect, useState } from 'react';

type Props = {
    stats?: DashboardStats;
    today_tasks?: TickTickTask[];
    overdue_tasks?: TickTickTask[];
    morning_tasks?: TickTickTask[];
    evening_tasks?: TickTickTask[];
    afternoon_tasks?: TickTickTask[];
    task_count?: number;
};

export default function Dashboard({ stats, today_tasks, overdue_tasks, morning_tasks, evening_tasks, afternoon_tasks, task_count }: Props) {
    const [value, setValue] = useState('0');
    const hasMorningTasks =
        morning_tasks !== undefined && morning_tasks.length > 0;
    const hasAfterNoonTasks =
        afternoon_tasks !== undefined && afternoon_tasks.length > 0;
    const hasEveningTasks =
        evening_tasks !== undefined && evening_tasks.length > 0;
    const hasTodayTasks = today_tasks !== undefined && today_tasks.length > 0;
    const hasOverdue = overdue_tasks !== undefined && overdue_tasks.length > 0;
    let defaultTab = hasMorningTasks ? '1' : '0';
    if(!hasMorningTasks){
        if(hasAfterNoonTasks){
            defaultTab = '2';
        }else if(hasEveningTasks){
            defaultTab = '3';
        }else if(hasTodayTasks){
            defaultTab = '4';
        }else if(hasOverdue){
            defaultTab = '5';
        }
    }

    useEffect(() => {
        setValue(defaultTab);
        const poll = router.poll(60000);
        return () => poll.stop();
    }, [defaultTab]);

    const handleChange = (event: React.SyntheticEvent, newValue: string) => {
        setValue(newValue);
    };

    return (
        <AppLayout>
            <Head title="Dashboard" />

            <div className="p-6">
                <h1 className="text-2xl font-semibold text-gray-900 dark:text-white">
                    Dashboard
                </h1>

                {value !== '0' ? (
                    <>
                        {/* Stats */}
                        <div className="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-3">
                            {stats ? (
                                <>
                                    <StatsCard
                                        label="Active Tasks"
                                        value={stats.total_active}
                                    />
                                    <StatsCard
                                        label="Due Today"
                                        value={stats.due_today}
                                        accent="warning"
                                    />
                                    <StatsCard
                                        label="Overdue"
                                        value={stats.overdue}
                                        accent="danger"
                                    />
                                </>
                            ) : (
                                <>
                                    <SkeletonCard />
                                    <SkeletonCard />
                                    <SkeletonCard />
                                </>
                            )}
                        </div>

                        <div className="flex w-full flex-col">
                            <TabContext value={value}>
                                <Box
                                    sx={{
                                        borderBottom: 1,
                                        borderColor: 'divider',
                                    }}
                                >
                                    <TabList
                                        onChange={handleChange}
                                        aria-label="lab API tabs example"
                                        textColor="primary"
                                    >
                                        {hasMorningTasks && (
                                            <Tab
                                                label="🌄️ Morning"
                                                value="1"
                                                sx={{ color: 'white' }}
                                            />
                                        )}

                                        {hasAfterNoonTasks && (
                                            <Tab
                                                label="☀️ Afternoon"
                                                value="2"
                                                sx={{ color: 'white' }}
                                            />
                                        )}

                                        {hasEveningTasks && (
                                            <Tab
                                                label="🌜 Evening"
                                                value="3"
                                                sx={{ color: 'white' }}
                                            />
                                        )}

                                        {hasTodayTasks && (
                                            <Tab
                                                label="📋 Today"
                                                value="4"
                                                sx={{ color: 'white' }}
                                            />
                                        )}
                                        {hasOverdue && (
                                            <Tab
                                                label="⏰ Overdue"
                                                value="5"
                                                sx={{ color: 'white' }}
                                            />
                                        )}
                                    </TabList>
                                </Box>
                                <TabPanel value="1" className="text-white">
                                    <div className="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                                        {hasMorningTasks &&
                                            morning_tasks.map((task) => (
                                                <TaskRow
                                                    key={task.id}
                                                    task={task}
                                                />
                                            ))}
                                    </div>
                                </TabPanel>
                                <TabPanel value="2" className="text-white">
                                    <div className="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                                        {hasAfterNoonTasks &&
                                            afternoon_tasks.map((task) => (
                                                <TaskRow
                                                    key={task.id}
                                                    task={task}
                                                />
                                            ))}
                                    </div>
                                </TabPanel>
                                <TabPanel value="3" className="text-white">
                                    <div className="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                                        {hasEveningTasks &&
                                            evening_tasks.map((task) => (
                                                <TaskRow
                                                    key={task.id}
                                                    task={task}
                                                />
                                            ))}
                                    </div>
                                </TabPanel>
                                <TabPanel value="4" className="text-white">
                                    <div className="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                                        {today_tasks === undefined ? (
                                            <SkeletonTaskList />
                                        ) : today_tasks.length === 0 ? (
                                            <EmptyState message="Nothing due today." />
                                        ) : (
                                            today_tasks.map((task) => (
                                                <TaskRow
                                                    key={task.id}
                                                    task={task}
                                                />
                                            ))
                                        )}
                                    </div>
                                </TabPanel>
                                <TabPanel value="5" className="text-white">
                                    <div className="divide-y divide-gray-100 overflow-hidden rounded-xl border border-gray-200 bg-white dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                                        {hasOverdue &&
                                            overdue_tasks.map((task) => (
                                                <TaskRow
                                                    key={task.id}
                                                    task={task}
                                                />
                                            ))}
                                    </div>
                                </TabPanel>
                            </TabContext>
                        </div>
                    </>
                ) : (
                    <div className="flex flex-col items-center justify-center p-8">
                        <p className="text-lg font-medium text-gray-500 dark:text-gray-400">No active tasks found!</p>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

function SkeletonTaskList() {
    return (
        <>
            {Array.from({ length: 3 }).map((_, i) => (
                <div key={i} className="animate-pulse px-4 py-3">
                    <div className="h-3.5 w-3/4 rounded bg-gray-200 dark:bg-gray-700" />
                    <div className="mt-2 h-2.5 w-1/3 rounded bg-gray-100 dark:bg-gray-700/60" />
                </div>
            ))}
        </>
    );
}

function EmptyState({ message }: { message: string }) {
    return (
        <p className="px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500">{message}</p>
    );
}

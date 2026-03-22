export type TickTickProject = {
    id: number;
    ticktick_id: string;
    name: string;
    color: string | null;
    view_mode: string | null;
    kind: 'TASK' | 'NOTE' | null;
    is_closed: boolean;
    sort_order: number | null;
    active_tasks_count?: number;
    total_tasks_count?: number;
};

export type TickTickTask = {
    id: number;
    ticktick_id: string;
    project_id: number | null;
    title: string;
    content: string | null;
    status: 0 | 2;
    priority: 0 | 1 | 3 | 5;
    start_date: string | null;
    due_date: string | null;
    completed_time: string | null;
    is_all_day: boolean;
    tags: string[];
    project: TickTickProject | null;
};

export type TickTickTag = {
    id: number;
    name: string;
    color: string | null;
};

export type DashboardStats = {
    total_active: number;
    due_today: number;
    overdue: number;
    completed_this_week: number;
};

export type PaginatedData<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
};

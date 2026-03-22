import type { TickTickProject } from '@/types';

type Props = {
    project: TickTickProject | null;
};

export default function ProjectPill({ project }: Props) {
    if (!project) {
        return null;
    }

    return (
        <span
            className="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium text-white"
            style={{ backgroundColor: project.color ?? '#6b7280' }}
        >
            {project.name}
        </span>
    );
}

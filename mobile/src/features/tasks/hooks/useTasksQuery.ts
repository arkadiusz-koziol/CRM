import { useQuery } from '@tanstack/react-query';
import { taskService } from '@/api/services/taskService';

export function useTasksQuery(params?: {
  page?: number;
  perPage?: number;
  search?: string;
  status?: string;
  priority?: string;
  assignedTo?: string;
  estateId?: string;
}) {
  return useQuery({
    queryKey: ['tasks', 'list', params],
    queryFn: () => taskService.getTasks(params),
    staleTime: 60_000, // 1 minute
  });
}

export function useTaskQuery(id: string) {
  return useQuery({
    queryKey: ['tasks', 'detail', id],
    queryFn: () => taskService.getTask(id),
    enabled: Boolean(id),
  });
}

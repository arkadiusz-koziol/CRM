import { useQuery } from '@tanstack/react-query';
import { userService } from '@/api/services/userService';

export function useUsersQuery(params?: {
  page?: number;
  perPage?: number;
  search?: string;
  status?: string;
  role?: string;
}) {
  return useQuery({
    queryKey: ['users', 'list', params],
    queryFn: () => userService.getUsers(params),
    staleTime: 60_000, // 1 minute
  });
}

export function useUserQuery(id: string) {
  return useQuery({
    queryKey: ['users', 'detail', id],
    queryFn: () => userService.getUser(id),
    enabled: Boolean(id),
  });
}

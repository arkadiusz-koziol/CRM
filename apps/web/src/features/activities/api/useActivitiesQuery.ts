import { useQuery } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';

const ActivitySchema = z.object({
  id: z.string(),
  action: z.string(),
  user_name: z.string(),
  user_email: z.string(),
  entity_type: z.string(),
  entity_id: z.string().nullable(),
  created_at: z.string(),
  time_ago: z.string(),
});

const ActivitiesResponseSchema = z.object({
  data: z.array(ActivitySchema),
  meta: z.object({
    total: z.number(),
    limit: z.number(),
  }),
});

export type Activity = z.infer<typeof ActivitySchema>;
export type ActivitiesResponse = z.infer<typeof ActivitiesResponseSchema>;

export function useActivitiesQuery(limit: number = 10) {
  return useQuery({
    queryKey: ['activities', 'list', { limit }],
    queryFn: () => http(`/api/v1/admin/activities/list?limit=${limit}`, {
      schema: (data) => ActivitiesResponseSchema.parse(data)
    }),
    staleTime: 30_000, // 30 seconds
    refetchInterval: 60_000, // Refetch every minute
  });
}

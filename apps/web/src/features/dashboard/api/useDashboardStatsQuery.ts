import { useQuery } from '@tanstack/react-query';
import { z } from 'zod';
import { getTokens } from '@/shared/lib/auth';
import { http } from '@/shared/lib/httpClient';

const DashboardStats = z.object({
  tools: z.object({
    total: z.number(),
    active: z.number(),
  }),
  materials: z.object({
    total: z.number(),
    active: z.number(),
  }),
  cars: z.object({
    total: z.number(),
    active: z.number(),
  }),
  estates: z.object({
    total: z.number(),
    active: z.number(),
  }),
});

export type DashboardStats = z.infer<typeof DashboardStats>;

export function useDashboardStatsQuery() {
  return useQuery({
    queryKey: ['dashboard', 'stats'],
    queryFn: async () => {
      const tokens = await getTokens();
      if (!tokens?.accessToken) {
        throw new Error('No access token available');
      }
      
      return http('/api/v1/admin/dashboard/stats', { 
        schema: (d) => DashboardStats.parse(d),
        headers: {
          'Authorization': `Bearer ${tokens.accessToken}`,
        }
      });
    },
    staleTime: 60_000, // Cache for 1 minute
  });
}

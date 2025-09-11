import { useQuery } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';
import { getTokens } from '@/shared/lib/auth';

const Tool = z.object({
  id: z.number(),
  name: z.string(),
  description: z.string(),
  count: z.number(),
  created_at: z.string().nullable(),
  updated_at: z.string().nullable(),
  deleted_at: z.string().nullable().optional(),
});

export type Tool = z.infer<typeof Tool>;

export function useToolQuery(toolId: number) {
  return useQuery({
    queryKey: ['tools', 'detail', toolId],
    queryFn: async () => {
      const tokens = await getTokens();
      if (!tokens?.accessToken) {
        throw new Error('No access token available');
      }
      
      return http(`/api/v1/admin/tools/${toolId}`, { 
        schema: (d) => Tool.parse(d),
        headers: {
          'Authorization': `Bearer ${tokens.accessToken}`,
        }
      });
    },
    staleTime: 60_000,
    enabled: !!toolId,
  });
}

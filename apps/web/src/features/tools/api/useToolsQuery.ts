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

const ToolsResponse = z.array(Tool);

export function useToolsQuery() {
  return useQuery({
    queryKey: ['tools', 'list'],
    queryFn: async () => {
      const tokens = await getTokens();
      if (!tokens?.accessToken) {
        throw new Error('No access token available');
      }
      
      return http('/api/v1/admin/tools/list', { 
        schema: (d) => ToolsResponse.parse(d),
        headers: {
          'Authorization': `Bearer ${tokens.accessToken}`,
        }
      });
    },
    staleTime: 60_000,
  });
}

import { useQuery } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';

const Tool = z.object({
  id: z.number(),
  name: z.string(),
  description: z.string(),
  count: z.number(),
  created_at: z.string(),
  updated_at: z.string(),
});

export type Tool = z.infer<typeof Tool>;

const ToolsResponse = z.object({
  data: z.array(Tool),
});

export function useToolsQuery() {
  return useQuery({
    queryKey: ['tools', 'list'],
    queryFn: () => http('/api/v1/admin/tools/list', { 
      schema: (d) => ToolsResponse.parse(d),
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('accessToken')}`,
      }
    }),
    staleTime: 60_000,
  });
}

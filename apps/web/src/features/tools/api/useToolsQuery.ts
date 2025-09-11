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

const PaginationInfo = z.object({
  current_page: z.number(),
  per_page: z.number(),
  total: z.number(),
  last_page: z.number(),
  from: z.number(),
  to: z.number(),
});

const ToolsResponse = z.object({
  data: z.array(Tool),
  pagination: PaginationInfo,
});

export type ToolsResponse = z.infer<typeof ToolsResponse>;
export type PaginationInfo = z.infer<typeof PaginationInfo>;

export function useToolsQuery(page: number = 1, limit: number = 12, search: string = '') {
  return useQuery({
    queryKey: ['tools', 'list', page, limit, search],
    queryFn: async () => {
      const tokens = await getTokens();
      if (!tokens?.accessToken) {
        throw new Error('No access token available');
      }
      
      const searchParam = search ? `&search=${encodeURIComponent(search)}` : '';
      return http(`/api/v1/admin/tools/list?page=${page}&limit=${limit}${searchParam}`, { 
        schema: (d) => ToolsResponse.parse(d),
        headers: {
          'Authorization': `Bearer ${tokens.accessToken}`,
        }
      });
    },
    staleTime: 60_000,
  });
}

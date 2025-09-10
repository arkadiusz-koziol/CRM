import { useQuery } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';

const Material = z.object({
  id: z.number(),
  name: z.string(),
  description: z.string(),
  count: z.number(),
  price: z.number(),
  created_at: z.string(),
  updated_at: z.string(),
});

export type Material = z.infer<typeof Material>;

const MaterialsResponse = z.object({
  data: z.array(Material),
});

export function useMaterialsQuery() {
  return useQuery({
    queryKey: ['materials', 'list'],
    queryFn: () => http('/api/v1/admin/materials/list', { 
      schema: (d) => MaterialsResponse.parse(d),
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('accessToken')}`,
      }
    }),
    staleTime: 60_000,
  });
}

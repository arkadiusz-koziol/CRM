import { useQuery } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';

const Estate = z.object({
  id: z.number(),
  name: z.string(),
  custom_id: z.string(),
  street: z.string(),
  postal_code: z.string(),
  city_id: z.number(),
  house_number: z.string(),
  created_at: z.string(),
  updated_at: z.string(),
});

export type Estate = z.infer<typeof Estate>;

const EstatesResponse = z.object({
  data: z.array(Estate),
});

export function useEstatesQuery() {
  return useQuery({
    queryKey: ['estates', 'list'],
    queryFn: () => http('/api/v1/admin/estates/list', { 
      schema: (d) => EstatesResponse.parse(d),
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('accessToken')}`,
      }
    }),
    staleTime: 60_000,
  });
}

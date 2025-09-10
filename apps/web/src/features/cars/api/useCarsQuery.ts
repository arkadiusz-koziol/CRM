import { useQuery } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';

const Car = z.object({
  id: z.number(),
  name: z.string(),
  description: z.string(),
  registration_number: z.string(),
  technical_details: z.string(),
  created_at: z.string(),
  updated_at: z.string(),
});

export type Car = z.infer<typeof Car>;

const CarsResponse = z.object({
  data: z.array(Car),
});

export function useCarsQuery() {
  return useQuery({
    queryKey: ['cars', 'list'],
    queryFn: () => http('/api/v1/admin/cars/list', { 
      schema: (d) => CarsResponse.parse(d),
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('accessToken')}`,
      }
    }),
    staleTime: 60_000,
  });
}

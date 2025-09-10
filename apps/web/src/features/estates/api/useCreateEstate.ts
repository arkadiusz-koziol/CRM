import { useMutation, useQueryClient } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';

const CreateEstateSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  custom_id: z.string().min(1, 'Custom ID is required'),
  street: z.string().min(1, 'Street is required'),
  postal_code: z.string().min(1, 'Postal code is required'),
  house_number: z.string().min(1, 'House number is required'),
  city: z.number().min(1, 'City is required'),
});

type CreateEstateData = z.infer<typeof CreateEstateSchema>;

export const createEstate = async (data: CreateEstateData) => {
  const response = await http.post('/estates', data);
  return response.data;
};

export const useCreateEstate = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: createEstate,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['estates'] });
    },
  });
};

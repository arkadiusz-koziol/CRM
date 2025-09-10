import { useMutation, useQueryClient } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';

const CreateMaterialSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  description: z.string().min(1, 'Description is required'),
  count: z.number().min(0, 'Count must be non-negative'),
  price: z.number().min(0, 'Price must be non-negative'),
});

type CreateMaterialData = z.infer<typeof CreateMaterialSchema>;

export const createMaterial = async (data: CreateMaterialData) => {
  const response = await http.post('/materials', data);
  return response.data;
};

export const useCreateMaterial = () => {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: createMaterial,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['materials'] });
    },
  });
};

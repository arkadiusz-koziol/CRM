import { useMutation, useQueryClient } from '@tanstack/react-query';
import { z } from 'zod';
import { http } from '@/shared/lib/httpClient';
import { getTokens } from '@/shared/lib/auth';

const CreateToolRequest = z.object({
  name: z.string().min(1, 'Nazwa jest wymagana'),
  description: z.string().min(1, 'Opis jest wymagany'),
  count: z.number().min(0, 'Liczba musi być większa lub równa 0'),
});

export type CreateToolRequest = z.infer<typeof CreateToolRequest>;

const Tool = z.object({
  id: z.number(),
  name: z.string(),
  description: z.string(),
  count: z.number(),
  created_at: z.string(),
  updated_at: z.string(),
});

export type Tool = z.infer<typeof Tool>;

export function useCreateTool() {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: CreateToolRequest) => {
      const tokens = await getTokens();
      if (!tokens?.accessToken) {
        throw new Error('No access token available');
      }
      
      return http('/api/v1/admin/tools', {
        method: 'POST',
        body: JSON.stringify(CreateToolRequest.parse(data)),
        headers: {
          'Authorization': `Bearer ${tokens.accessToken}`,
          'Content-Type': 'application/json',
        },
        schema: (d) => Tool.parse(d),
      });
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['tools'] });
    },
  });
}

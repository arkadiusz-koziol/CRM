import { z } from 'zod';

// JSON:API Task schema
export const TaskAttributes = z.object({
  title: z.string(),
  description: z.string().optional(),
  status: z.enum(['pending', 'in_progress', 'completed', 'cancelled']),
  priority: z.enum(['low', 'medium', 'high', 'urgent']),
  due_date: z.string().datetime().optional(),
  assigned_to: z.string().uuid().optional(),
  created_by: z.string().uuid(),
  estate_id: z.string().uuid().optional(),
  created_at: z.string().datetime(),
  updated_at: z.string().datetime(),
});

export const TaskJsonApi = z.object({
  type: z.literal('tasks'),
  id: z.string(),
  attributes: TaskAttributes,
  relationships: z.object({
    assigned_to: z.object({
      data: z.object({
        type: z.literal('users'),
        id: z.string(),
      }).optional(),
    }).optional(),
    created_by: z.object({
      data: z.object({
        type: z.literal('users'),
        id: z.string(),
      }),
    }),
    estate: z.object({
      data: z.object({
        type: z.literal('estates'),
        id: z.string(),
      }).optional(),
    }).optional(),
  }).optional(),
});

export const TasksListResponse = z.object({
  data: z.array(TaskJsonApi),
  meta: z.object({
    total: z.number().optional(),
    page: z.number().optional(),
    per_page: z.number().optional(),
  }).optional(),
  links: z.object({
    first: z.string().optional(),
    last: z.string().optional(),
    prev: z.string().optional(),
    next: z.string().optional(),
  }).optional(),
});

export const TaskResponse = z.object({
  data: TaskJsonApi,
});

export const CreateTaskRequest = z.object({
  data: z.object({
    type: z.literal('tasks'),
    attributes: z.object({
      title: z.string().min(1),
      description: z.string().optional(),
      priority: z.enum(['low', 'medium', 'high', 'urgent']),
      due_date: z.string().datetime().optional(),
      assigned_to: z.string().uuid().optional(),
      estate_id: z.string().uuid().optional(),
    }),
  }),
});

export const UpdateTaskRequest = z.object({
  data: z.object({
    type: z.literal('tasks'),
    id: z.string(),
    attributes: z.object({
      title: z.string().min(1).optional(),
      description: z.string().optional(),
      status: z.enum(['pending', 'in_progress', 'completed', 'cancelled']).optional(),
      priority: z.enum(['low', 'medium', 'high', 'urgent']).optional(),
      due_date: z.string().datetime().optional(),
      assigned_to: z.string().uuid().optional(),
      estate_id: z.string().uuid().optional(),
    }),
  }),
});

// Domain models
export type Task = {
  id: string;
  title: string;
  description?: string;
  status: 'pending' | 'in_progress' | 'completed' | 'cancelled';
  priority: 'low' | 'medium' | 'high' | 'urgent';
  dueDate?: string;
  assignedTo?: string;
  createdBy: string;
  estateId?: string;
  createdAt: string;
  updatedAt: string;
};

export type TasksList = z.infer<typeof TasksListResponse>;
export type TaskJsonApiType = z.infer<typeof TaskJsonApi>;
export type CreateTaskRequestType = z.infer<typeof CreateTaskRequest>;
export type UpdateTaskRequestType = z.infer<typeof UpdateTaskRequest>;

import { z } from 'zod';

// Task attributes schema
export const TaskAttributes = z.object({
  title: z.string().min(1),
  description: z.string().optional(),
  status: z.enum(['pending', 'in_progress', 'completed', 'cancelled']),
  priority: z.enum(['low', 'medium', 'high', 'urgent']),
  assignedTo: z.string().uuid().optional(),
  dueDate: z.string().datetime().optional(),
  completedAt: z.string().datetime().optional(),
  createdAt: z.string().datetime(),
  updatedAt: z.string().datetime(),
});

// Task JSON:API schema
export const TaskJsonApi = z.object({
  type: z.literal('tasks'),
  id: z.string().uuid(),
  attributes: TaskAttributes,
  relationships: z.object({
    assignedUser: z.object({
      data: z.object({
        type: z.literal('users'),
        id: z.string().uuid(),
      }).optional(),
    }).optional(),
  }).optional(),
});

// Task list response
export const TasksListResponse = z.object({
  data: z.array(TaskJsonApi),
  meta: z.object({
    total: z.number().optional(),
    page: z.number().optional(),
    perPage: z.number().optional(),
  }).optional(),
  links: z.object({
    first: z.string().url().optional(),
    last: z.string().url().optional(),
    prev: z.string().url().optional(),
    next: z.string().url().optional(),
  }).optional(),
});

// Task detail response
export const TaskDetailResponse = z.object({
  data: TaskJsonApi,
});

// Create task request
export const CreateTaskRequest = z.object({
  data: z.object({
    type: z.literal('tasks'),
    attributes: z.object({
      title: z.string().min(1),
      description: z.string().optional(),
      priority: z.enum(['low', 'medium', 'high', 'urgent']),
      assignedTo: z.string().uuid().optional(),
      dueDate: z.string().datetime().optional(),
    }),
  }),
});

// Update task request
export const UpdateTaskRequest = z.object({
  data: z.object({
    type: z.literal('tasks'),
    id: z.string().uuid(),
    attributes: z.object({
      title: z.string().min(1).optional(),
      description: z.string().optional(),
      status: z.enum(['pending', 'in_progress', 'completed', 'cancelled']).optional(),
      priority: z.enum(['low', 'medium', 'high', 'urgent']).optional(),
      assignedTo: z.string().uuid().optional(),
      dueDate: z.string().datetime().optional(),
    }),
  }),
});

// Export types
export type TaskAttributes = z.infer<typeof TaskAttributes>;
export type TaskJsonApi = z.infer<typeof TaskJsonApi>;
export type TasksListResponse = z.infer<typeof TasksListResponse>;
export type TaskDetailResponse = z.infer<typeof TaskDetailResponse>;
export type CreateTaskRequest = z.infer<typeof CreateTaskRequest>;
export type UpdateTaskRequest = z.infer<typeof UpdateTaskRequest>;

import { z } from 'zod';

// JSON:API User schema
export const UserAttributes = z.object({
  first_name: z.string(),
  last_name: z.string(),
  email: z.string().email(),
  phone: z.string().optional(),
  status: z.enum(['active', 'inactive', 'suspended']),
  role: z.enum(['admin', 'manager', 'technician', 'viewer']),
  created_at: z.string().datetime(),
  updated_at: z.string().datetime(),
});

export const UserJsonApi = z.object({
  type: z.literal('users'),
  id: z.string(),
  attributes: UserAttributes,
});

export const UsersListResponse = z.object({
  data: z.array(UserJsonApi),
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

export const UserResponse = z.object({
  data: UserJsonApi,
});

export const CreateUserRequest = z.object({
  data: z.object({
    type: z.literal('users'),
    attributes: z.object({
      first_name: z.string().min(1),
      last_name: z.string().min(1),
      email: z.string().email(),
      phone: z.string().optional(),
      role: z.enum(['admin', 'manager', 'technician', 'viewer']),
    }),
  }),
});

export const UpdateUserRequest = z.object({
  data: z.object({
    type: z.literal('users'),
    id: z.string(),
    attributes: z.object({
      first_name: z.string().min(1).optional(),
      last_name: z.string().min(1).optional(),
      email: z.string().email().optional(),
      phone: z.string().optional(),
      status: z.enum(['active', 'inactive', 'suspended']).optional(),
      role: z.enum(['admin', 'manager', 'technician', 'viewer']).optional(),
    }),
  }),
});

// Domain models
export type User = {
  id: string;
  firstName: string;
  lastName: string;
  email: string;
  phone?: string;
  status: 'active' | 'inactive' | 'suspended';
  role: 'admin' | 'manager' | 'technician' | 'viewer';
  createdAt: string;
  updatedAt: string;
};

export type UsersList = z.infer<typeof UsersListResponse>;
export type UserJsonApiType = z.infer<typeof UserJsonApi>;
export type CreateUserRequestType = z.infer<typeof CreateUserRequest>;
export type UpdateUserRequestType = z.infer<typeof UpdateUserRequest>;

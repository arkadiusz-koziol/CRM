import { z } from 'zod';

// User attributes schema
export const UserAttributes = z.object({
  firstName: z.string().min(1),
  lastName: z.string().min(1),
  email: z.string().email(),
  phone: z.string().optional(),
  status: z.enum(['active', 'inactive', 'suspended']),
  role: z.enum(['admin', 'manager', 'technician', 'user']),
  createdAt: z.string().datetime(),
  updatedAt: z.string().datetime(),
});

// User JSON:API schema
export const UserJsonApi = z.object({
  type: z.literal('users'),
  id: z.string().uuid(),
  attributes: UserAttributes,
});

// User list response
export const UsersListResponse = z.object({
  data: z.array(UserJsonApi),
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

// User detail response
export const UserDetailResponse = z.object({
  data: UserJsonApi,
});

// Create user request
export const CreateUserRequest = z.object({
  data: z.object({
    type: z.literal('users'),
    attributes: z.object({
      firstName: z.string().min(1),
      lastName: z.string().min(1),
      email: z.string().email(),
      phone: z.string().optional(),
      role: z.enum(['admin', 'manager', 'technician', 'user']),
    }),
  }),
});

// Update user request
export const UpdateUserRequest = z.object({
  data: z.object({
    type: z.literal('users'),
    id: z.string().uuid(),
    attributes: z.object({
      firstName: z.string().min(1).optional(),
      lastName: z.string().min(1).optional(),
      email: z.string().email().optional(),
      phone: z.string().optional(),
      status: z.enum(['active', 'inactive', 'suspended']).optional(),
      role: z.enum(['admin', 'manager', 'technician', 'user']).optional(),
    }),
  }),
});

// Export types
export type UserAttributes = z.infer<typeof UserAttributes>;
export type UserJsonApi = z.infer<typeof UserJsonApi>;
export type UsersListResponse = z.infer<typeof UsersListResponse>;
export type UserDetailResponse = z.infer<typeof UserDetailResponse>;
export type CreateUserRequest = z.infer<typeof CreateUserRequest>;
export type UpdateUserRequest = z.infer<typeof UpdateUserRequest>;

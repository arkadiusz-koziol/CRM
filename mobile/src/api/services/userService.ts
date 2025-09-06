import { httpClient } from '@/shared/lib/httpClient';
import {
  UsersListResponse,
  UserResponse,
  CreateUserRequest,
  UpdateUserRequest,
  User,
  UserJsonApiType,
} from '../schemas/user';

// Mapper functions to convert JSON:API to domain models
function mapUserFromJsonApi(jsonApiUser: UserJsonApiType): User {
  return {
    id: jsonApiUser.id,
    firstName: jsonApiUser.attributes.first_name,
    lastName: jsonApiUser.attributes.last_name,
    email: jsonApiUser.attributes.email,
    phone: jsonApiUser.attributes.phone,
    status: jsonApiUser.attributes.status,
    role: jsonApiUser.attributes.role,
    createdAt: jsonApiUser.attributes.created_at,
    updatedAt: jsonApiUser.attributes.updated_at,
  };
}

export const userService = {
  async getUsers(params?: {
    page?: number;
    perPage?: number;
    search?: string;
    status?: string;
    role?: string;
  }): Promise<{ users: User[]; meta?: any }> {
    const queryParams = new URLSearchParams();
    
    if (params?.page) queryParams.append('page', params.page.toString());
    if (params?.perPage) queryParams.append('per_page', params.perPage.toString());
    if (params?.search) queryParams.append('search', params.search);
    if (params?.status) queryParams.append('filter[status]', params.status);
    if (params?.role) queryParams.append('filter[role]', params.role);

    const queryString = queryParams.toString();
    const url = queryString ? `/users?${queryString}` : '/users';

    const response = await httpClient.get<UsersListResponse>(url, {
      schema: (data) => UsersListResponse.parse(data),
    });

    return {
      users: response.data.map(mapUserFromJsonApi),
      meta: response.meta,
    };
  },

  async getUser(id: string): Promise<User> {
    const response = await httpClient.get<UserResponse>(`/users/${id}`, {
      schema: (data) => UserResponse.parse(data),
    });

    return mapUserFromJsonApi(response.data);
  },

  async createUser(userData: CreateUserRequest): Promise<User> {
    const response = await httpClient.post<UserResponse>('/users', userData, {
      schema: (data) => UserResponse.parse(data),
    });

    return mapUserFromJsonApi(response.data);
  },

  async updateUser(id: string, userData: UpdateUserRequest): Promise<User> {
    const response = await httpClient.patch<UserResponse>(`/users/${id}`, userData, {
      schema: (data) => UserResponse.parse(data),
    });

    return mapUserFromJsonApi(response.data);
  },

  async deleteUser(id: string): Promise<void> {
    await httpClient.delete(`/users/${id}`);
  },
};

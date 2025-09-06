import { httpClient } from '@/shared/lib/httpClient';
import {
  TasksListResponse,
  TaskResponse,
  CreateTaskRequest,
  UpdateTaskRequest,
  Task,
  TaskJsonApiType,
} from '../schemas/task';

// Mapper functions to convert JSON:API to domain models
function mapTaskFromJsonApi(jsonApiTask: TaskJsonApiType): Task {
  return {
    id: jsonApiTask.id,
    title: jsonApiTask.attributes.title,
    description: jsonApiTask.attributes.description,
    status: jsonApiTask.attributes.status,
    priority: jsonApiTask.attributes.priority,
    dueDate: jsonApiTask.attributes.due_date,
    assignedTo: jsonApiTask.attributes.assigned_to,
    createdBy: jsonApiTask.attributes.created_by,
    estateId: jsonApiTask.attributes.estate_id,
    createdAt: jsonApiTask.attributes.created_at,
    updatedAt: jsonApiTask.attributes.updated_at,
  };
}

export const taskService = {
  async getTasks(params?: {
    page?: number;
    perPage?: number;
    search?: string;
    status?: string;
    priority?: string;
    assignedTo?: string;
    estateId?: string;
  }): Promise<{ tasks: Task[]; meta?: any }> {
    const queryParams = new URLSearchParams();
    
    if (params?.page) queryParams.append('page', params.page.toString());
    if (params?.perPage) queryParams.append('per_page', params.perPage.toString());
    if (params?.search) queryParams.append('search', params.search);
    if (params?.status) queryParams.append('filter[status]', params.status);
    if (params?.priority) queryParams.append('filter[priority]', params.priority);
    if (params?.assignedTo) queryParams.append('filter[assigned_to]', params.assignedTo);
    if (params?.estateId) queryParams.append('filter[estate_id]', params.estateId);

    const queryString = queryParams.toString();
    const url = queryString ? `/tasks?${queryString}` : '/tasks';

    const response = await httpClient.get<TasksListResponse>(url, {
      schema: (data) => TasksListResponse.parse(data),
    });

    return {
      tasks: response.data.map(mapTaskFromJsonApi),
      meta: response.meta,
    };
  },

  async getTask(id: string): Promise<Task> {
    const response = await httpClient.get<TaskResponse>(`/tasks/${id}`, {
      schema: (data) => TaskResponse.parse(data),
    });

    return mapTaskFromJsonApi(response.data);
  },

  async createTask(taskData: CreateTaskRequest): Promise<Task> {
    const response = await httpClient.post<TaskResponse>('/tasks', taskData, {
      schema: (data) => TaskResponse.parse(data),
    });

    return mapTaskFromJsonApi(response.data);
  },

  async updateTask(id: string, taskData: UpdateTaskRequest): Promise<Task> {
    const response = await httpClient.patch<TaskResponse>(`/tasks/${id}`, taskData, {
      schema: (data) => TaskResponse.parse(data),
    });

    return mapTaskFromJsonApi(response.data);
  },

  async deleteTask(id: string): Promise<void> {
    await httpClient.delete(`/tasks/${id}`);
  },
};

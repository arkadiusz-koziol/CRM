import { NextResponse } from 'next/server';
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { TasksListResponse } from '@skytech/api-sdk';

export async function GET() {
  // This is a BFF (Backend for Frontend) route
  // In a real implementation, this would call the actual backend API
  // For now, we'll return mock data
  
  const mockTasks = {
    data: [
      {
        type: 'tasks',
        id: '1',
        attributes: {
          title: 'Fix login issue',
          description: 'Users are unable to log in with their credentials',
          status: 'in_progress',
          priority: 'high',
          assignedTo: '1',
          dueDate: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString(),
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
        },
        relationships: {
          assignedUser: {
            data: {
              type: 'users',
              id: '1',
            },
          },
        },
      },
      {
        type: 'tasks',
        id: '2',
        attributes: {
          title: 'Update documentation',
          description: 'Update API documentation for new endpoints',
          status: 'pending',
          priority: 'medium',
          assignedTo: '2',
          dueDate: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000).toISOString(),
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
        },
        relationships: {
          assignedUser: {
            data: {
              type: 'users',
              id: '2',
            },
          },
        },
      },
    ],
    meta: {
      total: 2,
      page: 1,
      perPage: 10,
    },
  };

  return NextResponse.json(mockTasks);
}

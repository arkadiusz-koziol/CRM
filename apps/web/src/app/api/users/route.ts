import { NextResponse } from 'next/server';

export async function GET() {
  // This is a BFF (Backend for Frontend) route
  // In a real implementation, this would call the actual backend API
  // For now, we'll return mock data
  
  const mockUsers = {
    data: [
      {
        type: 'users',
        id: '1',
        attributes: {
          firstName: 'John',
          lastName: 'Doe',
          email: 'john.doe@example.com',
          phone: '+1234567890',
          status: 'active',
          role: 'admin',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
        },
      },
      {
        type: 'users',
        id: '2',
        attributes: {
          firstName: 'Jane',
          lastName: 'Smith',
          email: 'jane.smith@example.com',
          phone: '+1234567891',
          status: 'active',
          role: 'technician',
          createdAt: new Date().toISOString(),
          updatedAt: new Date().toISOString(),
        },
      },
    ],
    meta: {
      total: 2,
      page: 1,
      perPage: 10,
    },
  };

  return NextResponse.json(mockUsers);
}

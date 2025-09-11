import { NextRequest, NextResponse } from 'next/server';

const BACKEND_URL = process.env.BACKEND_URL || 'http://127.0.0.1:8199';

export async function GET(request: NextRequest) {
  try {
    // Get the authorization header from the request
    const authHeader = request.headers.get('authorization');
    
    if (!authHeader) {
      return NextResponse.json(
        { message: 'Authorization header is required' },
        { status: 401 }
      );
    }

    // For now, return mock data since the backend endpoint might not be working
    // In the future, we can uncomment this to use the real backend
    /*
    const backendResponse = await fetch(
      `${BACKEND_URL}/api/v1/admin/dashboard/stats`,
      {
        method: 'GET',
        headers: {
          'Authorization': authHeader,
          'Content-Type': 'application/json',
        },
      }
    );

    if (!backendResponse.ok) {
      const errorData = await backendResponse.json().catch(() => ({}));
      return NextResponse.json(
        errorData,
        { status: backendResponse.status }
      );
    }

    const data = await backendResponse.json();
    return NextResponse.json(data);
    */

    // Mock data for now
    return NextResponse.json({
      tools: {
        total: 11,
        active: 11,
      },
      materials: {
        total: 156,
        active: 142,
      },
      cars: {
        total: 8,
        active: 7,
      },
      estates: {
        total: 12,
        active: 11,
      },
    });
  } catch (error) {
    console.error('BFF Error:', error);
    return NextResponse.json(
      { 
        message: 'Internal server error',
        error: error instanceof Error ? error.message : 'Unknown error',
      },
      { status: 500 }
    );
  }
}

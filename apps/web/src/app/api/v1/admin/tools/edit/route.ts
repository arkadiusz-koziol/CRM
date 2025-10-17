import { NextRequest, NextResponse } from 'next/server';

const BACKEND_URL = process.env.BACKEND_URL || 'http://127.0.0.1:8199';

export async function PUT(request: NextRequest) {
  console.log('🔧 PUT request received for tool edit');
  
  try {
    // Get the authorization header from the request
    const authHeader = request.headers.get('authorization');
    console.log('🔑 Auth header present:', !!authHeader);
    
    if (!authHeader) {
      console.error('❌ No authorization header');
      return NextResponse.json(
        { message: 'Authorization header is required' },
        { status: 401 }
      );
    }

    // Get the request body
    const body = await request.json();
    console.log('📝 Request body:', body);

    // Get tool ID from query params
    const { searchParams } = new URL(request.url);
    const toolId = searchParams.get('id');
    
    if (!toolId || isNaN(Number(toolId))) {
      return NextResponse.json({ message: 'Invalid tool ID' }, { status: 400 });
    }

    console.log('🚀 Forwarding to backend:', `${BACKEND_URL}/api/v1/admin/tools/${toolId}`);
    
    // Forward the request to the backend
    const backendResponse = await fetch(
      `${BACKEND_URL}/api/v1/admin/tools/${toolId}`,
      {
        method: 'PUT',
        headers: {
          'Authorization': authHeader,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(body),
      }
    );
    
    console.log('📡 Backend response status:', backendResponse.status);

    if (!backendResponse.ok) {
      const errorData = await backendResponse.json().catch(() => ({}));
      return NextResponse.json(
        errorData,
        { status: backendResponse.status }
      );
    }

    const data = await backendResponse.json();
    return NextResponse.json(data);
  } catch (error) {
    console.error('BFF Error:', error);
    return NextResponse.json(
      { 
        message: 'Internal server error',
        error: error instanceof Error ? error.message : 'Unknown error'
      },
      { status: 500 }
    );
  }
}


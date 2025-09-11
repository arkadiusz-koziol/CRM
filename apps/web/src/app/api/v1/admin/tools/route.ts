import { NextRequest, NextResponse } from 'next/server';

const BACKEND_URL = process.env.BACKEND_URL || 'http://127.0.0.1:8199';

export async function POST(request: NextRequest) {
  try {
    // Get the authorization header from the request
    const authHeader = request.headers.get('authorization');
    
    if (!authHeader) {
      console.error('BFF Error: No authorization header');
      return NextResponse.json(
        { message: 'Authorization header is required' },
        { status: 401 }
      );
    }

    // Get the request body with better error handling
    let body;
    try {
      const bodyText = await request.text();
      console.log('BFF Request body:', bodyText);
      
      if (!bodyText || bodyText.trim() === '') {
        console.error('BFF Error: Empty request body');
        return NextResponse.json(
          { message: 'Request body is required' },
          { status: 400 }
        );
      }
      
      body = JSON.parse(bodyText);
    } catch (parseError) {
      console.error('BFF Error parsing JSON:', parseError);
      return NextResponse.json(
        { message: 'Invalid JSON in request body' },
        { status: 400 }
      );
    }
    
    console.log('BFF Forwarding to backend:', { body, authHeader: authHeader.substring(0, 20) + '...' });
    
    // Forward the request to the backend
    const backendResponse = await fetch(
      `${BACKEND_URL}/api/v1/admin/tools`,
      {
        method: 'POST',
        headers: {
          'Authorization': authHeader,
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(body),
      }
    );

    console.log('BFF Backend response status:', backendResponse.status);

    if (!backendResponse.ok) {
      const errorData = await backendResponse.json().catch(() => ({}));
      console.error('BFF Backend error:', errorData);
      return NextResponse.json(
        errorData,
        { status: backendResponse.status }
      );
    }

    const data = await backendResponse.json();
    console.log('BFF Success response:', data);
    return NextResponse.json(data);
  } catch (error) {
    console.error('BFF Error:', error);
    return NextResponse.json(
      { message: 'Internal server error', error: error instanceof Error ? error.message : 'Unknown error' },
      { status: 500 }
    );
  }
}

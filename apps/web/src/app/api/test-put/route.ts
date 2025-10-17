import { NextRequest, NextResponse } from 'next/server';

export async function PUT(request: NextRequest) {
  console.log('🔧 TEST PUT request received');
  return NextResponse.json({ message: 'PUT method works!' });
}

export async function GET(request: NextRequest) {
  console.log('🔧 TEST GET request received');
  return NextResponse.json({ message: 'GET method works!' });
}


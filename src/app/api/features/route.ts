import { NextResponse } from 'next/server';
import { db } from '@/lib/db';

// GET /api/features - Get all features
export async function GET() {
  try {
    const features = await db.feature.findMany({
      orderBy: { name: 'asc' },
    });
    return NextResponse.json(features);
  } catch (error) {
    console.error('Error fetching features:', error);
    return NextResponse.json(
      { error: 'Failed to fetch features' },
      { status: 500 }
    );
  }
}

// POST /api/features - Create a new feature
export async function POST(request: Request) {
  try {
    const body = await request.json();

    if (!body.name || body.name.trim() === '') {
      return NextResponse.json(
        { error: 'Feature name is required' },
        { status: 400 }
      );
    }

    // Check if feature already exists
    const existingFeature = await db.feature.findUnique({
      where: { name: body.name.trim() },
    });

    if (existingFeature) {
      return NextResponse.json(
        { error: 'Feature already exists' },
        { status: 400 }
      );
    }

    const feature = await db.feature.create({
      data: {
        name: body.name.trim(),
        icon: body.icon?.trim() || null,
      },
    });

    return NextResponse.json(feature, { status: 201 });
  } catch (error) {
    console.error('Error creating feature:', error);
    return NextResponse.json(
      { error: 'Failed to create feature' },
      { status: 500 }
    );
  }
}

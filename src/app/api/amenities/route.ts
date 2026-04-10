import { NextResponse } from 'next/server';
import { db } from '@/lib/db';

// GET /api/amenities - Get all amenities
export async function GET() {
  try {
    const amenities = await db.amenity.findMany({
      orderBy: { name: 'asc' },
    });
    return NextResponse.json(amenities);
  } catch (error) {
    console.error('Error fetching amenities:', error);
    return NextResponse.json(
      { error: 'Failed to fetch amenities' },
      { status: 500 }
    );
  }
}

// POST /api/amenities - Create a new amenity
export async function POST(request: Request) {
  try {
    const body = await request.json();

    if (!body.name || body.name.trim() === '') {
      return NextResponse.json(
        { error: 'Amenity name is required' },
        { status: 400 }
      );
    }

    // Check if amenity already exists
    const existingAmenity = await db.amenity.findUnique({
      where: { name: body.name.trim() },
    });

    if (existingAmenity) {
      return NextResponse.json(
        { error: 'Amenity already exists' },
        { status: 400 }
      );
    }

    const amenity = await db.amenity.create({
      data: {
        name: body.name.trim(),
        icon: body.icon?.trim() || null,
      },
    });

    return NextResponse.json(amenity, { status: 201 });
  } catch (error) {
    console.error('Error creating amenity:', error);
    return NextResponse.json(
      { error: 'Failed to create amenity' },
      { status: 500 }
    );
  }
}

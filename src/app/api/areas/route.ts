import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { getSession } from '@/lib/auth';

// GET /api/areas - Get all areas (optionally filtered by city)
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const cityId = searchParams.get('cityId');

    const areas = await db.area.findMany({
      where: cityId ? { cityId } : undefined,
      orderBy: { name: 'asc' },
      include: {
        city: {
          include: {
            state: true,
          },
        },
      },
    });
    return NextResponse.json(areas);
  } catch (error) {
    console.error('Error fetching areas:', error);
    return NextResponse.json(
      { error: 'Failed to fetch areas' },
      { status: 500 }
    );
  }
}

// POST /api/areas - Create a new area
export async function POST(request: Request) {
  try {
    const session = await getSession();
    if (!session) {
      return NextResponse.json(
        { error: 'Unauthorized. Please log in as admin.' },
        { status: 401 }
      );
    }

    const body = await request.json();

    if (!body.name || body.name.trim() === '') {
      return NextResponse.json(
        { error: 'Area name is required' },
        { status: 400 }
      );
    }

    // Check if area already exists in this city (or globally if no city)
    const existingArea = await db.area.findFirst({
      where: {
        name: body.name.trim(),
        cityId: body.cityId || null,
      },
    });

    if (existingArea) {
      return NextResponse.json(
        { error: 'Area already exists in this city' },
        { status: 400 }
      );
    }

    const area = await db.area.create({
      data: {
        name: body.name.trim(),
        cityId: body.cityId || null,
      },
      include: {
        city: {
          include: {
            state: true,
          },
        },
      },
    });

    return NextResponse.json(area, { status: 201 });
  } catch (error) {
    console.error('Error creating area:', error);
    return NextResponse.json(
      { error: 'Failed to create area' },
      { status: 500 }
    );
  }
}

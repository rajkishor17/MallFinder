import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { getSession } from '@/lib/auth';

// GET /api/cities - Get all cities (optionally filtered by state)
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const stateId = searchParams.get('stateId');

    const cities = await db.city.findMany({
      where: stateId ? { stateId } : undefined,
      orderBy: { name: 'asc' },
      include: {
        state: true,
        areas: {
          orderBy: { name: 'asc' },
        },
      },
    });
    return NextResponse.json(cities);
  } catch (error) {
    console.error('Error fetching cities:', error);
    return NextResponse.json(
      { error: 'Failed to fetch cities' },
      { status: 500 }
    );
  }
}

// POST /api/cities - Create a new city
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
        { error: 'City name is required' },
        { status: 400 }
      );
    }

    if (!body.stateId) {
      return NextResponse.json(
        { error: 'State is required' },
        { status: 400 }
      );
    }

    // Check if city already exists in this state
    const existingCity = await db.city.findFirst({
      where: {
        name: body.name.trim(),
        stateId: body.stateId,
      },
    });

    if (existingCity) {
      return NextResponse.json(
        { error: 'City already exists in this state' },
        { status: 400 }
      );
    }

    const city = await db.city.create({
      data: {
        name: body.name.trim(),
        stateId: body.stateId,
      },
      include: {
        state: true,
      },
    });

    return NextResponse.json(city, { status: 201 });
  } catch (error) {
    console.error('Error creating city:', error);
    return NextResponse.json(
      { error: 'Failed to create city' },
      { status: 500 }
    );
  }
}

import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { getSession } from '@/lib/auth';

// GET /api/states - Get all states with their cities
export async function GET() {
  try {
    const states = await db.state.findMany({
      orderBy: { name: 'asc' },
      include: {
        cities: {
          orderBy: { name: 'asc' },
          include: {
            areas: {
              orderBy: { name: 'asc' },
            },
          },
        },
      },
    });
    return NextResponse.json(states);
  } catch (error) {
    console.error('Error fetching states:', error);
    return NextResponse.json(
      { error: 'Failed to fetch states' },
      { status: 500 }
    );
  }
}

// POST /api/states - Create a new state
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
        { error: 'State name is required' },
        { status: 400 }
      );
    }

    // Check if state already exists
    const existingState = await db.state.findUnique({
      where: { name: body.name.trim() },
    });

    if (existingState) {
      return NextResponse.json(
        { error: 'State already exists' },
        { status: 400 }
      );
    }

    const state = await db.state.create({
      data: {
        name: body.name.trim(),
      },
    });

    return NextResponse.json(state, { status: 201 });
  } catch (error) {
    console.error('Error creating state:', error);
    return NextResponse.json(
      { error: 'Failed to create state' },
      { status: 500 }
    );
  }
}

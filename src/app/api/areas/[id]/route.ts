import { NextResponse } from 'next/server';
import { db } from '@/lib/db';

// GET /api/areas/[id] - Get a single area
export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const area = await db.area.findUnique({
      where: { id },
    });

    if (!area) {
      return NextResponse.json(
        { error: 'Area not found' },
        { status: 404 }
      );
    }

    return NextResponse.json(area);
  } catch (error) {
    console.error('Error fetching area:', error);
    return NextResponse.json(
      { error: 'Failed to fetch area' },
      { status: 500 }
    );
  }
}

// PUT /api/areas/[id] - Update an area
export async function PUT(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    const body = await request.json();
    const { name } = body;

    if (!name || typeof name !== 'string' || !name.trim()) {
      return NextResponse.json(
        { error: 'Area name is required' },
        { status: 400 }
      );
    }

    // Check if area exists
    const existingArea = await db.area.findUnique({
      where: { id },
    });

    if (!existingArea) {
      return NextResponse.json(
        { error: 'Area not found' },
        { status: 404 }
      );
    }

    // Check if another area with the same name already exists
    const duplicateArea = await db.area.findFirst({
      where: {
        name: name.trim(),
        NOT: { id },
      },
    });

    if (duplicateArea) {
      return NextResponse.json(
        { error: 'An area with this name already exists' },
        { status: 400 }
      );
    }

    const updatedArea = await db.area.update({
      where: { id },
      data: { name: name.trim() },
    });

    return NextResponse.json(updatedArea);
  } catch (error) {
    console.error('Error updating area:', error);
    return NextResponse.json(
      { error: 'Failed to update area' },
      { status: 500 }
    );
  }
}

// DELETE /api/areas/[id] - Delete an area
export async function DELETE(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const existingArea = await db.area.findUnique({
      where: { id },
    });

    if (!existingArea) {
      return NextResponse.json(
        { error: 'Area not found' },
        { status: 404 }
      );
    }

    await db.area.delete({
      where: { id },
    });

    return NextResponse.json({ message: 'Area deleted successfully' });
  } catch (error) {
    console.error('Error deleting area:', error);
    return NextResponse.json(
      { error: 'Failed to delete area' },
      { status: 500 }
    );
  }
}

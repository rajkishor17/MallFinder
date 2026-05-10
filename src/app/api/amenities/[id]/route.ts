import { NextResponse } from 'next/server';
import { db } from '@/lib/db';

// PUT /api/amenities/[id] - Update an amenity
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
        { error: 'Amenity name is required' },
        { status: 400 }
      );
    }

    const existingAmenity = await db.amenity.findUnique({
      where: { id },
    });

    if (!existingAmenity) {
      return NextResponse.json(
        { error: 'Amenity not found' },
        { status: 404 }
      );
    }

    // Check if another amenity with the same name exists
    const duplicateAmenity = await db.amenity.findFirst({
      where: {
        name: name.trim(),
        NOT: { id },
      },
    });

    if (duplicateAmenity) {
      return NextResponse.json(
        { error: 'An amenity with this name already exists' },
        { status: 400 }
      );
    }

    const updatedAmenity = await db.amenity.update({
      where: { id },
      data: { name: name.trim() },
    });

    return NextResponse.json(updatedAmenity);
  } catch (error) {
    console.error('Error updating amenity:', error);
    return NextResponse.json(
      { error: 'Failed to update amenity' },
      { status: 500 }
    );
  }
}

// DELETE /api/amenities/[id] - Delete an amenity
export async function DELETE(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const existingAmenity = await db.amenity.findUnique({
      where: { id },
    });

    if (!existingAmenity) {
      return NextResponse.json(
        { error: 'Amenity not found' },
        { status: 404 }
      );
    }

    await db.amenity.delete({
      where: { id },
    });

    return NextResponse.json({ message: 'Amenity deleted successfully' });
  } catch (error) {
    console.error('Error deleting amenity:', error);
    return NextResponse.json(
      { error: 'Failed to delete amenity' },
      { status: 500 }
    );
  }
}

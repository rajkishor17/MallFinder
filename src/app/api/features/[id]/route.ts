import { NextResponse } from 'next/server';
import { db } from '@/lib/db';

// PUT /api/features/[id] - Update a feature
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
        { error: 'Feature name is required' },
        { status: 400 }
      );
    }

    const existingFeature = await db.feature.findUnique({
      where: { id },
    });

    if (!existingFeature) {
      return NextResponse.json(
        { error: 'Feature not found' },
        { status: 404 }
      );
    }

    // Check if another feature with the same name exists
    const duplicateFeature = await db.feature.findFirst({
      where: {
        name: name.trim(),
        NOT: { id },
      },
    });

    if (duplicateFeature) {
      return NextResponse.json(
        { error: 'A feature with this name already exists' },
        { status: 400 }
      );
    }

    const updatedFeature = await db.feature.update({
      where: { id },
      data: { name: name.trim() },
    });

    return NextResponse.json(updatedFeature);
  } catch (error) {
    console.error('Error updating feature:', error);
    return NextResponse.json(
      { error: 'Failed to update feature' },
      { status: 500 }
    );
  }
}

// DELETE /api/features/[id] - Delete a feature
export async function DELETE(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const existingFeature = await db.feature.findUnique({
      where: { id },
    });

    if (!existingFeature) {
      return NextResponse.json(
        { error: 'Feature not found' },
        { status: 404 }
      );
    }

    await db.feature.delete({
      where: { id },
    });

    return NextResponse.json({ message: 'Feature deleted successfully' });
  } catch (error) {
    console.error('Error deleting feature:', error);
    return NextResponse.json(
      { error: 'Failed to delete feature' },
      { status: 500 }
    );
  }
}

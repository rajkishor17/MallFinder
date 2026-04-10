import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { getSession } from '@/lib/auth';
import { AdPosition, AdType } from '@prisma/client';

// GET /api/advertisements/[id] - Get a single advertisement
export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;

    const advertisement = await db.advertisement.findUnique({
      where: { id },
      include: {
        mall: {
          select: {
            id: true,
            name: true,
          },
        },
      },
    });

    if (!advertisement) {
      return NextResponse.json(
        { error: 'Advertisement not found' },
        { status: 404 }
      );
    }

    return NextResponse.json(advertisement);
  } catch (error) {
    console.error('Error fetching advertisement:', error);
    return NextResponse.json(
      { error: 'Failed to fetch advertisement' },
      { status: 500 }
    );
  }
}

// PUT /api/advertisements/[id] - Update an advertisement
export async function PUT(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    // Check authentication
    const session = await getSession();
    if (!session) {
      return NextResponse.json(
        { error: 'Unauthorized. Please log in as admin.' },
        { status: 401 }
      );
    }

    const { id } = await params;
    const body = await request.json();

    // Check if advertisement exists
    const existingAd = await db.advertisement.findUnique({
      where: { id },
    });

    if (!existingAd) {
      return NextResponse.json(
        { error: 'Advertisement not found' },
        { status: 404 }
      );
    }

    const { title, position, type, imageUrl, linkUrl, htmlCode, isActive, sortOrder, mallId } = body;

    // Build update data
    const updateData: {
      title?: string;
      position?: AdPosition;
      type?: AdType;
      imageUrl?: string | null;
      linkUrl?: string | null;
      htmlCode?: string | null;
      isActive?: boolean;
      sortOrder?: number;
      mallId?: string | null;
    } = {};

    if (title !== undefined) updateData.title = title;
    if (position !== undefined) updateData.position = position.toUpperCase() as AdPosition;
    if (type !== undefined) updateData.type = type.toUpperCase() as AdType;
    if (imageUrl !== undefined) updateData.imageUrl = imageUrl || null;
    if (linkUrl !== undefined) updateData.linkUrl = linkUrl || null;
    if (htmlCode !== undefined) updateData.htmlCode = htmlCode || null;
    if (isActive !== undefined) updateData.isActive = isActive;
    if (sortOrder !== undefined) updateData.sortOrder = sortOrder;
    if (mallId !== undefined) updateData.mallId = mallId || null;

    const advertisement = await db.advertisement.update({
      where: { id },
      data: updateData,
      include: {
        mall: {
          select: {
            id: true,
            name: true,
          },
        },
      },
    });

    return NextResponse.json(advertisement);
  } catch (error) {
    console.error('Error updating advertisement:', error);
    return NextResponse.json(
      { error: 'Failed to update advertisement' },
      { status: 500 }
    );
  }
}

// DELETE /api/advertisements/[id] - Delete an advertisement
export async function DELETE(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    // Check authentication
    const session = await getSession();
    if (!session) {
      return NextResponse.json(
        { error: 'Unauthorized. Please log in as admin.' },
        { status: 401 }
      );
    }

    const { id } = await params;

    // Check if advertisement exists
    const existingAd = await db.advertisement.findUnique({
      where: { id },
    });

    if (!existingAd) {
      return NextResponse.json(
        { error: 'Advertisement not found' },
        { status: 404 }
      );
    }

    // Delete the advertisement
    await db.advertisement.delete({
      where: { id },
    });

    return NextResponse.json({ message: 'Advertisement deleted successfully' });
  } catch (error) {
    console.error('Error deleting advertisement:', error);
    return NextResponse.json(
      { error: 'Failed to delete advertisement' },
      { status: 500 }
    );
  }
}

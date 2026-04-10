import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { getSession } from '@/lib/auth';
import { AdPosition, AdType } from '@prisma/client';

// GET /api/advertisements - Get all advertisements
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const position = searchParams.get('position');
    const mallId = searchParams.get('mallId');
    const activeOnly = searchParams.get('active') === 'true';

    const where: {
      position?: AdPosition;
      mallId?: string | null;
      isActive?: boolean;
    } = {};

    if (position) {
      where.position = position.toUpperCase() as AdPosition;
    }

    // If mallId is provided, get ads for that mall (both mall-specific and global ads)
    if (mallId) {
      where.mallId = mallId;
    }

    if (activeOnly) {
      where.isActive = true;
    }

    const advertisements = await db.advertisement.findMany({
      where,
      include: {
        mall: {
          select: {
            id: true,
            name: true,
          },
        },
      },
      orderBy: { sortOrder: 'asc' },
    });

    return NextResponse.json(advertisements);
  } catch (error) {
    console.error('Error fetching advertisements:', error);
    return NextResponse.json(
      { error: 'Failed to fetch advertisements' },
      { status: 500 }
    );
  }
}

// POST /api/advertisements - Create a new advertisement
export async function POST(request: Request) {
  try {
    // Check authentication
    const session = await getSession();
    if (!session) {
      return NextResponse.json(
        { error: 'Unauthorized. Please log in as admin.' },
        { status: 401 }
      );
    }

    const body = await request.json();
    const { title, position, type, imageUrl, linkUrl, htmlCode, isActive, sortOrder, mallId } = body;

    // Validate required fields
    if (!title || !position || !type) {
      return NextResponse.json(
        { error: 'Title, position, and type are required' },
        { status: 400 }
      );
    }

    // Validate type-specific fields
    if (type === 'IMAGE' && !imageUrl) {
      return NextResponse.json(
        { error: 'Image URL is required for image type advertisements' },
        { status: 400 }
      );
    }

    if (type === 'HTML' && !htmlCode) {
      return NextResponse.json(
        { error: 'HTML code is required for HTML type advertisements' },
        { status: 400 }
      );
    }

    const advertisement = await db.advertisement.create({
      data: {
        title,
        position: position.toUpperCase() as AdPosition,
        type: type.toUpperCase() as AdType,
        imageUrl: imageUrl || null,
        linkUrl: linkUrl || null,
        htmlCode: htmlCode || null,
        isActive: isActive ?? true,
        sortOrder: sortOrder ?? 0,
        mallId: mallId || null,
      },
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
    console.error('Error creating advertisement:', error);
    return NextResponse.json(
      { error: 'Failed to create advertisement' },
      { status: 500 }
    );
  }
}

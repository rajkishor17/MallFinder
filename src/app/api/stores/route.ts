import { NextResponse } from 'next/server';
import { StoreStatus } from '@prisma/client';
import prisma from '@/lib/prisma-store';
import { getSession } from '@/lib/auth';

// GET /api/stores - Get all stores with optional filters
export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const mallId = searchParams.get('mallId');
    const category = searchParams.get('category');
    const status = searchParams.get('status');
    
    const stores = await prisma.store.findMany({
      where: {
        ...(mallId ? { mallId } : {}),
        ...(category ? { category } : {}),
        ...(status ? { status: status as StoreStatus } : {}),
      },
      include: {
        mall: {
          select: {
            id: true,
            name: true,
            address: true,
          },
        },
        images: {
          orderBy: { sortOrder: 'asc' },
        },
      },
      orderBy: { createdAt: 'desc' },
    });
    
    return NextResponse.json(stores);
  } catch (error) {
    console.error('Error fetching stores:', error);
    return NextResponse.json([]);
  }
}

// POST /api/stores - Create a new store
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
    const images = body.images || [];
    
    const store = await prisma.store.create({
      data: {
        mallId: body.mallId,
        name: body.name,
        description: body.description || null,
        category: body.category,
        floor: body.floor || null,
        unitNumber: body.unitNumber || null,
        phone: body.phone || null,
        website: body.website || null,
        imageUrl: images[0]?.url || body.imageUrl || null,
        status: (body.status || 'OPEN').toUpperCase() as StoreStatus,
        openingHours: body.openingHours || null,
        // Create images if provided
        ...(images.length > 0 ? {
          images: {
            create: images.map((img: { url: string; sortOrder: number }, index: number) => ({
              url: img.url,
              sortOrder: img.sortOrder ?? index,
            })),
          }
        } : {}),
      },
      include: {
        mall: {
          select: {
            id: true,
            name: true,
            address: true,
          },
        },
        images: true,
      },
    });
    
    return NextResponse.json(store, { status: 201 });
  } catch (error) {
    console.error('Error creating store:', error);
    return NextResponse.json(
      { error: 'Failed to create store' },
      { status: 500 }
    );
  }
}

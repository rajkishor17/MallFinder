import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { StoreStatus } from '@prisma/client';

// GET /api/malls/[id]/stores - Get all stores for a specific mall
export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    const { searchParams } = new URL(request.url);
    const category = searchParams.get('category');
    const status = searchParams.get('status');
    
    // Check if mall exists
    const mall = await db.mall.findUnique({
      where: { id },
    });
    
    if (!mall) {
      return NextResponse.json(
        { error: 'Mall not found' },
        { status: 404 }
      );
    }
    
    // Build where clause
    const where = {
      mallId: id,
      ...(category ? { category } : {}),
      ...(status ? { status: status.toUpperCase() as StoreStatus } : {}),
    };
    
    const stores = await db.store.findMany({
      where,
      include: {
        images: {
          orderBy: { sortOrder: 'asc' },
        },
      },
      orderBy: [{ floor: 'asc' }, { name: 'asc' }],
    });

    return NextResponse.json(stores);
  } catch (error) {
    console.error('Error fetching mall stores:', error);
    return NextResponse.json([]);
  }
}

import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { getSession } from '@/lib/auth';

// GET /api/malls/[id] - Get a single mall
export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    
    const mall = await db.mall.findUnique({
      where: { id },
      include: {
        _count: {
          select: { stores: true },
        },
      },
    });

    if (!mall) {
      return NextResponse.json(
        { error: 'Mall not found' },
        { status: 404 }
      );
    }

    // Transform the data to match the frontend interface
    const transformedMall = {
      id: mall.id,
      name: mall.name,
      description: mall.description || '',
      address: mall.address || '',
      area: mall.area || '',
      city: mall.city || '',
      state: mall.state || '',
      coordinates: {
        lat: mall.latitude || 0,
        lng: mall.longitude || 0,
      },
      features: mall.features
        ? mall.features.split(',').map((f) => f.trim())
        : [],
      storesCount: mall._count.stores,
      amenities: mall.amenities
        ? mall.amenities.split(',').map((a) => a.trim())
        : [],
      openingHours: mall.openingHours || undefined,
      expectedOpeningDate: mall.expectedOpeningDate || undefined,
      imageUrl: mall.imageUrl || '/malls/default.jpg',
      category: mall.status.toLowerCase() as 'existing' | 'upcoming',
      status: mall.status,
      // SEO Fields
      metaTitle: mall.metaTitle || undefined,
      metaDescription: mall.metaDescription || undefined,
      keywords: mall.keywords || undefined,
      ogImage: mall.ogImage || undefined,
      canonicalUrl: mall.canonicalUrl || undefined,
      createdAt: mall.createdAt,
      updatedAt: mall.updatedAt,
    };

    return NextResponse.json(transformedMall);
  } catch (error) {
    console.error('Error fetching mall:', error);
    return NextResponse.json(
      { error: 'Failed to fetch mall' },
      { status: 500 }
    );
  }
}

// PUT /api/malls/[id] - Update a mall
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
    console.log('[Mall Update] Received body:', JSON.stringify(body, null, 2));
    console.log('[Mall Update] City:', body.city, 'State:', body.state);
    
    // Check if mall exists
    const existingMall = await db.mall.findUnique({
      where: { id },
    });
    
    if (!existingMall) {
      return NextResponse.json(
        { error: 'Mall not found' },
        { status: 404 }
      );
    }
    
    // Update the mall
    const mall = await db.mall.update({
      where: { id },
      data: {
        name: body.name,
        description: body.description || null,
        address: body.address || null,
        area: body.area || null,
        city: body.city && body.city.trim() !== '' ? body.city.trim() : null,
        state: body.state && body.state.trim() !== '' ? body.state.trim() : null,
        latitude: body.latitude ? parseFloat(body.latitude) : null,
        longitude: body.longitude ? parseFloat(body.longitude) : null,
        imageUrl: body.imageUrl || null,
        status: body.status ? body.status.toUpperCase() : undefined,
        openingHours: body.openingHours || null,
        expectedOpeningDate: body.expectedOpeningDate || null,
        features: Array.isArray(body.features) ? body.features.join(',') : body.features || null,
        storesCount: body.storesCount ? parseInt(body.storesCount) : null,
        amenities: Array.isArray(body.amenities) ? body.amenities.join(',') : body.amenities || null,
        // SEO Fields
        metaTitle: body.metaTitle || null,
        metaDescription: body.metaDescription || null,
        keywords: body.keywords || null,
        ogImage: body.ogImage || null,
        canonicalUrl: body.canonicalUrl || null,
      },
    });
    
    console.log('[Mall Update] Updated mall:', { city: mall.city, state: mall.state });
    
    return NextResponse.json(mall);
  } catch (error) {
    console.error('Error updating mall:', error);
    return NextResponse.json(
      { error: 'Failed to update mall' },
      { status: 500 }
    );
  }
}

// DELETE /api/malls/[id] - Delete a mall
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
    
    // Check if mall exists
    const existingMall = await db.mall.findUnique({
      where: { id },
    });
    
    if (!existingMall) {
      return NextResponse.json(
        { error: 'Mall not found' },
        { status: 404 }
      );
    }
    
    // Delete the mall
    await db.mall.delete({
      where: { id },
    });
    
    return NextResponse.json({ message: 'Mall deleted successfully' });
  } catch (error) {
    console.error('Error deleting mall:', error);
    return NextResponse.json(
      { error: 'Failed to delete mall' },
      { status: 500 }
    );
  }
}

import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { MallStatus } from '@prisma/client';
import { getSession } from '@/lib/auth';

// Malls API - handles GET (list) and POST (create) operations

// GET /api/malls - Get all malls with optional status filter
export async function GET(request: Request) {
  try {
    console.log('[Malls API] Starting GET request');
    const { searchParams } = new URL(request.url);
    const status = searchParams.get('status');
    console.log('[Malls API] Query params:', { status });
    
    console.log('[Malls API] Attempting database query...');
    const malls = await db.mall.findMany({
      where: status ? { status: status as MallStatus } : undefined,
      orderBy: { createdAt: 'desc' },
      include: {
        _count: {
          select: { stores: true },
        },
      },
    });
    console.log('[Malls API] Found malls:', malls.length);
    
    // Transform the data to match the frontend interface
    const transformedMalls = malls.map(mall => ({
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
      features: mall.features ? mall.features.split(',').map(f => f.trim()) : [],
      storesCount: mall._count.stores,
      amenities: mall.amenities ? mall.amenities.split(',').map(a => a.trim()) : [],
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
    }));
    
    return NextResponse.json(transformedMalls);
  } catch (error) {
    console.error('Error fetching malls:', error);
    return NextResponse.json(
      { error: 'Failed to fetch malls' },
      { status: 500 }
    );
  }
}

// POST /api/malls - Create a new mall
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
    
    const mall = await db.mall.create({
      data: {
        name: body.name,
        description: body.description || null,
        address: body.address || null,
        area: body.area || null,
        city: body.city || null,
        state: body.state || null,
        latitude: body.latitude ? parseFloat(body.latitude) : null,
        longitude: body.longitude ? parseFloat(body.longitude) : null,
        imageUrl: body.imageUrl || null,
        status: (body.status || body.category || 'EXISTING').toUpperCase() as MallStatus,
        openingHours: body.openingHours || null,
        expectedOpeningDate: body.expectedOpeningDate || null,
        features: Array.isArray(body.features) ? body.features.join(',') : body.features || null,
        storesCount: body.storesCount ? parseInt(body.storesCount) : null,
        amenities: Array.isArray(body.amenities) ? body.amenities.join(',') : body.amenities || null,
      },
    });
    
    return NextResponse.json(mall, { status: 201 });
  } catch (error) {
    console.error('Error creating mall:', error);
    return NextResponse.json(
      { error: 'Failed to create mall' },
      { status: 500 }
    );
  }
}

import { NextResponse } from 'next/server';
import { StoreStatus } from '@prisma/client';
import prisma from '@/lib/prisma-store';
import { getSession } from '@/lib/auth';

// GET /api/stores/[id] - Get a single store
export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const { id } = await params;
    
    const store = await prisma.store.findUnique({
      where: { id },
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
    });
    
    if (!store) {
      return NextResponse.json(
        { error: 'Store not found' },
        { status: 404 }
      );
    }
    
    return NextResponse.json(store);
  } catch (error) {
    console.error('Error fetching store:', error);
    return NextResponse.json(
      { error: 'Failed to fetch store' },
      { status: 500 }
    );
  }
}

// PUT /api/stores/[id] - Update a store
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
    
    const existingStore = await prisma.store.findUnique({
      where: { id },
      include: { images: true },
    });
    
    if (!existingStore) {
      return NextResponse.json(
        { error: 'Store not found' },
        { status: 404 }
      );
    }
    
    // Update store
    const store = await prisma.store.update({
      where: { id },
      data: {
        name: body.name,
        description: body.description || null,
        category: body.category,
        floor: body.floor || null,
        unitNumber: body.unitNumber || null,
        phone: body.phone || null,
        website: body.website || null,
        imageUrl: body.imageUrl || null,
        status: (body.status || 'OPEN').toUpperCase() as StoreStatus,
        openingHours: body.openingHours || null,
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
    });
    
    // Handle images update if provided
    if (body.images && Array.isArray(body.images)) {
      // Delete existing images
      await prisma.storeImage.deleteMany({
        where: { storeId: id },
      });
      
      // Create new images
      if (body.images.length > 0) {
        await prisma.storeImage.createMany({
          data: body.images.map((img: { url: string; sortOrder: number }) => ({
            storeId: id,
            url: img.url,
            sortOrder: img.sortOrder,
          })),
        });
      }
      
      // Update main imageUrl to first image
      if (body.images.length > 0) {
        await prisma.store.update({
          where: { id },
          data: { imageUrl: body.images[0].url },
        });
      }
    }
    
    // Fetch updated store with images
    const updatedStore = await prisma.store.findUnique({
      where: { id },
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
    });
    
    return NextResponse.json(updatedStore);
  } catch (error) {
    console.error('Error updating store:', error);
    return NextResponse.json(
      { error: 'Failed to update store' },
      { status: 500 }
    );
  }
}

// DELETE /api/stores/[id] - Delete a store
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
    
    const existingStore = await prisma.store.findUnique({
      where: { id },
    });
    
    if (!existingStore) {
      return NextResponse.json(
        { error: 'Store not found' },
        { status: 404 }
      );
    }
    
    await prisma.store.delete({
      where: { id },
    });
    
    return NextResponse.json({ message: 'Store deleted successfully' });
  } catch (error) {
    console.error('Error deleting store:', error);
    return NextResponse.json(
      { error: 'Failed to delete store' },
      { status: 500 }
    );
  }
}

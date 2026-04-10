import { NextResponse } from 'next/server';
import { db } from '@/lib/db';

export async function GET() {
  try {
    const pages = await db.page.findMany({
      orderBy: { createdAt: 'desc' },
    });
    return NextResponse.json(pages);
  } catch (error) {
    console.error('Failed to fetch pages:', error);
    return NextResponse.json(
      { error: 'Failed to fetch pages' },
      { status: 500 }
    );
  }
}

export async function POST(request: Request) {
  try {
    const data = await request.json();
    const page = await db.page.create({
      data: {
        slug: data.slug,
        title: data.title,
        content: data.content,
        metaTitle: data.metaTitle || null,
        metaDescription: data.metaDescription || null,
      },
    });
    return NextResponse.json(page, { status: 201 });
  } catch (error) {
    console.error('Failed to create page:', error);
    return NextResponse.json(
      { error: 'Failed to create page' },
      { status: 500 }
    );
  }
}

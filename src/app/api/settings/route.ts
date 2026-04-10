import { NextResponse } from 'next/server';
import { db } from '@/lib/db';
import { getSession } from '@/lib/auth';

// GET /api/settings - Get all site settings
export async function GET() {
  try {
    const settings = await db.siteSettings.findMany();
    
    // Convert array to object
    const settingsObject: Record<string, string> = {};
    settings.forEach((setting) => {
      settingsObject[setting.key] = setting.value || '';
    });
    
    return NextResponse.json({
      siteName: settingsObject.siteName || 'MallFinder',
      siteTagline: settingsObject.siteTagline || 'Discover Shopping Destinations',
      siteLogo: settingsObject.siteLogo || '',
      // Social Links
      facebook: settingsObject.facebook || '',
      twitter: settingsObject.twitter || '',
      instagram: settingsObject.instagram || '',
      linkedin: settingsObject.linkedin || '',
      youtube: settingsObject.youtube || '',
      whatsapp: settingsObject.whatsapp || '',
    });
  } catch (error) {
    console.error('Failed to fetch settings:', error);
    return NextResponse.json(
      { error: 'Failed to fetch settings' },
      { status: 500 }
    );
  }
}

// PUT /api/settings - Update site settings
export async function PUT(request: Request) {
  try {
    // Check authentication
    const session = await getSession();
    if (!session) {
      return NextResponse.json(
        { error: 'Unauthorized' },
        { status: 401 }
      );
    }

    const data = await request.json();
    
    // Define all possible setting keys
    const settingKeys = [
      'siteName',
      'siteTagline',
      'siteLogo',
      'facebook',
      'twitter',
      'instagram',
      'linkedin',
      'youtube',
      'whatsapp',
    ];
    
    // Update each setting
    const updates = [];
    
    for (const key of settingKeys) {
      if (data[key] !== undefined) {
        updates.push(
          db.siteSettings.upsert({
            where: { key },
            update: { value: data[key] },
            create: { key, value: data[key] },
          })
        );
      }
    }
    
    if (updates.length > 0) {
      await Promise.all(updates);
    }
    
    return NextResponse.json({ success: true });
  } catch (error) {
    console.error('Failed to update settings:', error);
    return NextResponse.json(
      { error: 'Failed to update settings' },
      { status: 500 }
    );
  }
}

// POST /api/settings - Update site settings (alias for PUT)
export async function POST(request: Request) {
  return PUT(request);
}

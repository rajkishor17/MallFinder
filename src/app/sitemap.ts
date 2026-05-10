import { MetadataRoute } from 'next';
import { db } from '@/lib/db';

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const baseUrl = 'https://mallfinder.app';

  // Get all malls
  const malls = await db.mall.findMany({
    select: {
      id: true,
      name: true,
      updatedAt: true,
    },
  });

  // Get all stores with their mall info
  const stores = await db.store.findMany({
    select: {
      id: true,
      name: true,
      updatedAt: true,
      mall: {
        select: {
          id: true,
        },
      },
    },
  });

  // Static pages
  const staticPages: MetadataRoute.Sitemap = [
    {
      url: baseUrl,
      lastModified: new Date(),
      changeFrequency: 'daily',
      priority: 1,
    },
    {
      url: `${baseUrl}/about`,
      lastModified: new Date(),
      changeFrequency: 'monthly',
      priority: 0.5,
    },
    {
      url: `${baseUrl}/contact`,
      lastModified: new Date(),
      changeFrequency: 'monthly',
      priority: 0.5,
    },
  ];

  // Mall pages
  const mallPages: MetadataRoute.Sitemap = malls.map((mall) => ({
    url: `${baseUrl}/mall/${mall.id}`,
    lastModified: mall.updatedAt,
    changeFrequency: 'weekly' as const,
    priority: 0.8,
  }));

  // Store pages (if we add individual store pages in the future)
  const storePages: MetadataRoute.Sitemap = stores.map((store) => ({
    url: `${baseUrl}/mall/${store.mall.id}?store=${store.id}`,
    lastModified: store.updatedAt,
    changeFrequency: 'weekly' as const,
    priority: 0.6,
  }));

  // Areas pages (if we add area listing pages)
  const areas = await db.area.findMany();
  const areaPages: MetadataRoute.Sitemap = areas.map((area) => ({
    url: `${baseUrl}/area/${area.name.toLowerCase().replace(/\s+/g, '-')}`,
    lastModified: area.updatedAt,
    changeFrequency: 'weekly' as const,
    priority: 0.6,
  }));

  // Categories pages
  const categories = await db.storeCategory.findMany();
  const categoryPages: MetadataRoute.Sitemap = categories.map((category) => ({
    url: `${baseUrl}/category/${category.name.toLowerCase().replace(/\s+/g, '-')}`,
    lastModified: category.updatedAt,
    changeFrequency: 'weekly' as const,
    priority: 0.6,
  }));

  return [...staticPages, ...mallPages, ...storePages, ...areaPages, ...categoryPages];
}

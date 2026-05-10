import { Metadata } from 'next';
import { db } from '@/lib/db';
import { SITE_CONFIG } from '@/lib/seo';

// Generate static params for known mall IDs (optional optimization)
export async function generateStaticParams() {
  const malls = await db.mall.findMany({
    select: { id: true },
  });
  
  return malls.map((mall) => ({
    id: mall.id,
  }));
}

// Generate metadata for SEO
export async function generateMetadata({
  params,
}: {
  params: Promise<{ id: string }>;
}): Promise<Metadata> {
  const { id } = await params;
  
  try {
    const mall = await db.mall.findUnique({
      where: { id },
    });

    if (!mall) {
      return {
        title: 'Mall Not Found | MallFinder',
        description: 'The requested mall could not be found.',
      };
    }

    // Generate SEO-optimized title and description
    const title = mall.metaTitle || `${mall.name} - Shopping Mall Directory | MallFinder`;
    const description = mall.metaDescription || 
      mall.description ||
      `Discover ${mall.name} in ${mall.area || 'the city'}. Browse ${mall.storesCount || 'numerous'} stores, ${mall.status === 'EXISTING' ? 'now open' : 'coming soon'}. Find directions, hours, and store directory.`;
    
    const keywords = mall.keywords || 
      `${mall.name}, ${mall.area} shopping, shopping mall, retail stores, ${mall.status === 'EXISTING' ? 'open now' : 'coming soon'}, mall directory`;
    
    const ogImage = mall.ogImage || mall.imageUrl || SITE_CONFIG.ogImage;
    const canonicalUrl = mall.canonicalUrl || `${SITE_CONFIG.url}/mall/${mall.id}`;

    return {
      title,
      description,
      keywords,
      alternates: {
        canonical: canonicalUrl,
      },
      openGraph: {
        title,
        description,
        type: 'website',
        url: canonicalUrl,
        siteName: SITE_CONFIG.name,
        images: ogImage ? [
          {
            url: ogImage,
            width: 1200,
            height: 630,
            alt: mall.name,
          },
        ] : [],
        locale: SITE_CONFIG.locale,
      },
      twitter: {
        card: 'summary_large_image',
        title,
        description,
        images: ogImage ? [ogImage] : [],
        creator: SITE_CONFIG.twitterHandle,
      },
      robots: {
        index: true,
        follow: true,
        googleBot: {
          index: true,
          follow: true,
          'max-video-preview': -1,
          'max-image-preview': 'large',
          'max-snippet': -1,
        },
      },
    };
  } catch (error) {
    console.error('Error generating metadata:', error);
    return {
      title: 'Mall | MallFinder',
      description: 'Explore shopping malls and stores.',
    };
  }
}

export default function MallLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return <>{children}</>;
}

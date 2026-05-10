import { notFound } from 'next/navigation';
import { db } from '@/lib/db';
import { PublicPage } from '@/components/page/PublicPage';

interface PageProps {
  params: Promise<{ slug: string }>;
}

// Generate static params for all pages
export async function generateStaticParams() {
  const pages = await db.page.findMany({
    select: { slug: true },
  });
  
  return pages.map((page) => ({
    slug: page.slug,
  }));
}

// Get page data
async function getPage(slug: string) {
  const page = await db.page.findUnique({
    where: { slug },
  });
  
  return page;
}

export default async function DynamicPage({ params }: PageProps) {
  const { slug } = await params;
  const page = await getPage(slug);

  if (!page) {
    notFound();
  }

  return <PublicPage page={page} />;
}

// Generate metadata for SEO
export async function generateMetadata({ params }: PageProps) {
  const { slug } = await params;
  const page = await getPage(slug);

  if (!page) {
    return {
      title: 'Page Not Found',
    };
  }

  return {
    title: page.metaTitle || `${page.title} | MallFinder`,
    description: page.metaDescription || page.title,
  };
}

// SEO Configuration and Utilities

export const SITE_CONFIG = {
  name: 'MallFinder',
  description: 'Discover the best shopping malls near you. Explore existing malls and upcoming shopping destinations with interactive maps and detailed information.',
  url: 'https://mallfinder.app',
  ogImage: '/og-image.png',
  twitterHandle: '@mallfinder',
  locale: 'en_US',
};

export interface SEOData {
  title: string;
  description: string;
  keywords?: string[];
  ogImage?: string;
  canonicalUrl?: string;
  noIndex?: boolean;
}

export interface LocalBusinessStructuredData {
  '@context': 'https://schema.org';
  '@type': 'ShoppingMall' | 'Store';
  name: string;
  description?: string;
  address?: {
    '@type': 'PostalAddress';
    streetAddress?: string;
    addressLocality?: string;
    addressRegion?: string;
    postalCode?: string;
    addressCountry?: string;
  };
  geo?: {
    '@type': 'GeoCoordinates';
    latitude: number;
    longitude: number;
  };
  url?: string;
  telephone?: string;
  openingHours?: string;
  image?: string;
  priceRange?: string;
}

export interface BreadcrumbStructuredData {
  '@context': 'https://schema.org';
  '@type': 'BreadcrumbList';
  itemListElement: Array<{
    '@type': 'ListItem';
    position: number;
    name: string;
    item?: string;
  }>;
}

// Generate meta title with fallback
export function generateMetaTitle(title: string, suffix = SITE_CONFIG.name): string {
  if (title.length > 60) {
    return title.substring(0, 57) + '...';
  }
  return `${title} | ${suffix}`;
}

// Generate meta description with fallback
export function generateMetaDescription(description: string, fallback: string): string {
  const finalDescription = description || fallback;
  if (finalDescription.length > 160) {
    return finalDescription.substring(0, 157) + '...';
  }
  return finalDescription;
}

// Generate keywords string
export function generateKeywords(keywords: string[] = [], additional: string[] = []): string {
  const allKeywords = [...keywords, ...additional, 'shopping mall', 'retail', 'stores'];
  return [...new Set(allKeywords)].join(', ');
}

// Generate canonical URL
export function generateCanonicalUrl(path: string): string {
  return `${SITE_CONFIG.url}${path.startsWith('/') ? path : `/${path}`}`;
}

// Generate JSON-LD for Shopping Mall
export function generateMallStructuredData(mall: {
  name: string;
  description?: string | null;
  address?: string | null;
  area?: string | null;
  latitude?: number | null;
  longitude?: number | null;
  imageUrl?: string | null;
  openingHours?: string | null;
  status: string;
}): LocalBusinessStructuredData {
  return {
    '@context': 'https://schema.org',
    '@type': 'ShoppingMall',
    name: mall.name,
    description: mall.description || undefined,
    address: mall.address ? {
      '@type': 'PostalAddress',
      streetAddress: mall.address,
      addressLocality: mall.area || undefined,
      addressCountry: 'US',
    } : undefined,
    geo: (mall.latitude && mall.longitude) ? {
      '@type': 'GeoCoordinates',
      latitude: mall.latitude,
      longitude: mall.longitude,
    } : undefined,
    image: mall.imageUrl || undefined,
    openingHours: mall.status === 'EXISTING' ? (mall.openingHours || undefined) : undefined,
  };
}

// Generate JSON-LD for Store
export function generateStoreStructuredData(store: {
  name: string;
  description?: string | null;
  category: string;
  floor?: string | null;
  phone?: string | null;
  imageUrl?: string | null;
  openingHours?: string | null;
  status: string;
  mall: {
    name: string;
    address?: string | null;
    area?: string | null;
  };
}): LocalBusinessStructuredData {
  return {
    '@type': 'Store',
    '@context': 'https://schema.org',
    name: store.name,
    description: store.description || `${store.name} - ${store.category} store at ${store.mall.name}`,
    address: store.mall.address ? {
      '@type': 'PostalAddress',
      streetAddress: `${store.floor ? `${store.floor}, ` : ''}${store.mall.address}`,
      addressLocality: store.mall.area || undefined,
      addressCountry: 'US',
    } : undefined,
    telephone: store.phone || undefined,
    image: store.imageUrl || undefined,
    openingHours: store.status === 'OPEN' ? (store.openingHours || undefined) : undefined,
  };
}

// Generate breadcrumb structured data
export function generateBreadcrumbData(items: Array<{ name: string; url?: string }>): BreadcrumbStructuredData {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: item.name,
      item: item.url ? `${SITE_CONFIG.url}${item.url}` : undefined,
    })),
  };
}

// Generate aggregate rating structured data
export function generateAggregateRatingData(rating: {
  ratingValue: number;
  reviewCount: number;
  bestRating?: number;
  worstRating?: number;
}) {
  return {
    '@context': 'https://schema.org',
    '@type': 'AggregateRating',
    ratingValue: rating.ratingValue,
    reviewCount: rating.reviewCount,
    bestRating: rating.bestRating || 5,
    worstRating: rating.worstRating || 1,
  };
}

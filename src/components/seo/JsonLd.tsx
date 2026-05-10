'use client';

import Script from 'next/script';
import type { Mall } from '@/types/mall';
import type { Store } from '@/types/store';

interface JsonLdProps {
  mall: Mall;
  stores?: Store[];
}

export function MallJsonLd({ mall, stores = [] }: JsonLdProps) {
  // Generate ShoppingMall structured data
  const mallStructuredData = {
    '@context': 'https://schema.org',
    '@type': 'ShoppingMall',
    '@id': `https://mallfinder.app/mall/${mall.id}`,
    name: mall.name,
    description: mall.description || `${mall.name} - Shopping mall in ${mall.area}`,
    address: {
      '@type': 'PostalAddress',
      streetAddress: mall.address,
      addressLocality: mall.area,
      addressCountry: 'US',
    },
    geo: mall.coordinates ? {
      '@type': 'GeoCoordinates',
      latitude: mall.coordinates.lat,
      longitude: mall.coordinates.lng,
    } : undefined,
    url: `https://mallfinder.app/mall/${mall.id}`,
    image: mall.imageUrl,
    telephone: mall.phone || undefined,
    openingHours: mall.openingHours || undefined,
    priceRange: '$$',
    hasMap: mall.coordinates 
      ? `https://www.google.com/maps?q=${mall.coordinates.lat},${mall.coordinates.lng}`
      : undefined,
    amenityFeature: mall.amenities?.map((amenity) => ({
      '@type': 'LocationFeatureSpecification',
      name: amenity,
      value: true,
    })),
    numberOfStores: mall.storesCount || stores.length,
  };

  // Generate BreadcrumbList structured data
  const breadcrumbData = {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: [
      {
        '@type': 'ListItem',
        position: 1,
        name: 'Home',
        item: 'https://mallfinder.app',
      },
      {
        '@type': 'ListItem',
        position: 2,
        name: mall.category === 'existing' ? 'Existing Malls' : 'Upcoming Malls',
        item: `https://mallfinder.app/?category=${mall.category}`,
      },
      {
        '@type': 'ListItem',
        position: 3,
        name: mall.name,
        item: `https://mallfinder.app/mall/${mall.id}`,
      },
    ],
  };

  // Generate Store structured data for each store
  const storeStructuredData = stores.slice(0, 10).map((store) => ({
    '@context': 'https://schema.org',
    '@type': 'Store',
    '@id': `https://mallfinder.app/mall/${mall.id}?store=${store.id}`,
    name: store.name,
    description: store.description || `${store.name} - ${store.category} store at ${mall.name}`,
    address: {
      '@type': 'PostalAddress',
      streetAddress: store.floor ? `${store.floor}, ${mall.address}` : mall.address,
      addressLocality: mall.area,
      addressCountry: 'US',
    },
    telephone: store.phone || undefined,
    url: store.website || undefined,
    image: store.imageUrl || undefined,
    openingHours: store.openingHours || undefined,
    containedInPlace: {
      '@type': 'ShoppingMall',
      '@id': `https://mallfinder.app/mall/${mall.id}`,
    },
  }));

  // Combine all structured data
  const structuredDataArray = [
    mallStructuredData,
    breadcrumbData,
    ...storeStructuredData,
  ].filter(Boolean);

  return (
    <>
      {structuredDataArray.map((data, index) => (
        <Script
          key={index}
          id={`json-ld-${index}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
        />
      ))}
    </>
  );
}

// Organization JSON-LD for the entire site
export function OrganizationJsonLd() {
  const data = {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: 'MallFinder',
    url: 'https://mallfinder.app',
    logo: 'https://mallfinder.app/logo.svg',
    description: 'Discover the best shopping malls near you. Explore existing malls and upcoming shopping destinations.',
    sameAs: [
      'https://twitter.com/mallfinder',
      'https://facebook.com/mallfinder',
      'https://instagram.com/mallfinder',
      'https://pinterest.com/mallfinder',
      'https://wa.me/919876543210',
    ],
    contactPoint: {
      '@type': 'ContactPoint',
      telephone: '+1-800-MALLFIND',
      contactType: 'customer service',
      availableLanguage: ['English'],
    },
  };

  return (
    <Script
      id="json-ld-organization"
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
    />
  );
}

// WebSite JSON-LD for sitelinks search box
export function WebSiteJsonLd() {
  const data = {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    name: 'MallFinder',
    url: 'https://mallfinder.app',
    potentialAction: {
      '@type': 'SearchAction',
      target: {
        '@type': 'EntryPoint',
        urlTemplate: 'https://mallfinder.app/?search={search_term_string}',
      },
      'query-input': 'required name=search_term_string',
    },
  };

  return (
    <Script
      id="json-ld-website"
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
    />
  );
}

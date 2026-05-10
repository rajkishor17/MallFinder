export interface Mall {
  id: string;
  name: string;
  description: string | null;
  address: string | null;
  area: string;
  city: string | null;
  state: string | null;
  coordinates: {
    lat: number;
    lng: number;
  };
  imageUrl: string | null;
  category: 'existing' | 'upcoming';
  status: 'EXISTING' | 'UPCOMING';
  openingHours: string | null;
  expectedOpeningDate: string | null;
  features: string[];
  amenities: string[];
  storesCount: number;
  // SEO Fields
  metaTitle: string | null;
  metaDescription: string | null;
  keywords: string | null;
  ogImage: string | null;
  canonicalUrl: string | null;
  createdAt: string | Date;
  updatedAt: string | Date;
  phone?: string;
}

export interface MallFormData {
  name: string;
  description: string;
  address: string;
  area: string;
  city: string;
  state: string;
  latitude: string;
  longitude: string;
  imageUrl: string;
  status: 'EXISTING' | 'UPCOMING';
  openingHours: string;
  expectedOpeningDate: string;
  features: string;
  amenities: string;
  metaTitle: string;
  metaDescription: string;
  keywords: string;
  ogImage: string;
  canonicalUrl: string;
}

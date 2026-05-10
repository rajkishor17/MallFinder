export interface Store {
  id: string;
  mallId: string;
  name: string;
  description: string | null;
  category: string;
  floor: string | null;
  unitNumber: string | null;
  phone: string | null;
  website: string | null;
  imageUrl: string | null;
  status: 'OPEN' | 'COMING_SOON' | 'CLOSED';
  openingHours: string | null;
  weekendHours?: string | null;
  sundayHours?: string | null;
  // SEO Fields
  metaTitle: string | null;
  metaDescription: string | null;
  keywords: string | null;
  ogImage: string | null;
  createdAt: string | Date;
  updatedAt: string | Date;
  images: StoreImage[];
}

export interface StoreImage {
  id: string;
  storeId: string;
  url: string;
  sortOrder: number;
  createdAt: string | Date;
  updatedAt: string | Date;
}

export interface StoreFormData {
  name: string;
  description: string;
  category: string;
  floor: string;
  unitNumber: string;
  phone: string;
  website: string;
  imageUrl: string;
  status: 'OPEN' | 'COMING_SOON' | 'CLOSED';
  openingHours: string;
  weekendHours?: string;
  sundayHours?: string;
  metaTitle: string;
  metaDescription: string;
  keywords: string;
  ogImage: string;
}

export const DEFAULT_STORE_CATEGORIES = [
  'Fashion & Apparel',
  'Electronics',
  'Food & Dining',
  'Entertainment',
  'Health & Beauty',
  'Home & Living',
  'Sports & Outdoors',
  'Books & Stationery',
  'Jewelry & Accessories',
  'Services',
  'Other'
];

export const MAX_STORE_IMAGES = 5;

export type AdPosition = 'HEADER' | 'FOOTER' | 'LEFT_SIDEBAR' | 'RIGHT_SIDEBAR' | 'MALL_HEADER' | 'MALL_FOOTER' | 'MALL_SIDEBAR';
export type AdType = 'IMAGE' | 'HTML';

export interface Advertisement {
  id: string;
  title: string;
  position: AdPosition;
  type: AdType;
  imageUrl: string | null;
  linkUrl: string | null;
  htmlCode: string | null;
  isActive: boolean;
  sortOrder: number;
  mallId: string | null;
  createdAt: string;
  updatedAt: string;
  mall?: {
    id: string;
    name: string;
  };
}

export const AD_POSITIONS: { value: AdPosition; label: string; description: string; isMallSpecific: boolean }[] = [
  { value: 'HEADER', label: 'Site Header', description: 'Top of all pages (global)', isMallSpecific: false },
  { value: 'FOOTER', label: 'Site Footer', description: 'Bottom of all pages (global)', isMallSpecific: false },
  { value: 'LEFT_SIDEBAR', label: 'Left Sidebar', description: 'Left sidebar on homepage (global)', isMallSpecific: false },
  { value: 'RIGHT_SIDEBAR', label: 'Right Sidebar', description: 'Right sidebar on homepage (global)', isMallSpecific: false },
  { value: 'MALL_HEADER', label: 'Mall Header', description: 'Top of individual mall pages', isMallSpecific: true },
  { value: 'MALL_FOOTER', label: 'Mall Footer', description: 'Bottom of individual mall pages', isMallSpecific: true },
  { value: 'MALL_SIDEBAR', label: 'Mall Sidebar', description: 'Sidebar on individual mall pages', isMallSpecific: true },
];

export const AD_TYPES: { value: AdType; label: string }[] = [
  { value: 'IMAGE', label: 'Image with Link' },
  { value: 'HTML', label: 'HTML Code' },
];

// Helper to check if position is mall-specific
export function isMallSpecificPosition(position: AdPosition): boolean {
  const pos = AD_POSITIONS.find(p => p.value === position);
  return pos?.isMallSpecific ?? false;
}

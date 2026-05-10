'use client';

import { useEffect, useState } from 'react';
import type { Advertisement, AdPosition } from '@/types/advertisement';

interface MallAdvertisementBannerProps {
  mallId: string;
  fallbackPosition?: AdPosition;
  className?: string;
}

/**
 * MallAdvertisementBanner - Shows mall-specific ads with fallback to global ads
 * 
 * This component first tries to fetch mall-specific ads for the mall page.
 * If no mall-specific ads are found, it falls back to global ads.
 */
export function MallAdvertisementBanner({ 
  mallId, 
  fallbackPosition,
  className = '' 
}: MallAdvertisementBannerProps) {
  const [advertisements, setAdvertisements] = useState<Advertisement[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchAdvertisements = async () => {
      try {
        // First, try to get mall-specific ads
        const params = new URLSearchParams();
        params.set('mallId', mallId);
        params.set('active', 'true');

        const response = await fetch(`/api/advertisements?${params.toString()}`);
        if (response.ok) {
          const data = await response.json();
          
          // If we have mall-specific ads, use them
          if (data && data.length > 0) {
            setAdvertisements(data);
          } else if (fallbackPosition) {
            // Otherwise, fall back to global ads
            const fallbackParams = new URLSearchParams();
            fallbackParams.set('position', fallbackPosition);
            fallbackParams.set('active', 'true');
            
            const fallbackResponse = await fetch(`/api/advertisements?${fallbackParams.toString()}`);
            if (fallbackResponse.ok) {
              const fallbackData = await fallbackResponse.json();
              setAdvertisements(fallbackData);
            }
          }
        }
      } catch (error) {
        console.error('Error fetching advertisements:', error);
      } finally {
        setLoading(false);
      }
    };

    if (mallId) {
      fetchAdvertisements();
    }
  }, [mallId, fallbackPosition]);

  // Don't render anything while loading or if no ads
  if (loading || advertisements.length === 0) {
    return null;
  }

  // Get the first active advertisement
  const ad = advertisements[0];

  if (!ad) {
    return null;
  }

  // Render based on position type
  const isSidebar = ad.position === 'MALL_SIDEBAR';
  const isHeader = ad.position === 'MALL_HEADER';
  const isFooter = ad.position === 'MALL_FOOTER';

  // Container classes based on position
  const containerClasses = isHeader
    ? 'w-full bg-slate-100 dark:bg-slate-800 py-2 px-4'
    : isFooter
    ? 'w-full bg-slate-100 dark:bg-slate-800 py-3 px-4'
    : isSidebar
    ? 'w-full'
    : '';

  // Ad content classes
  const adClasses = isHeader
    ? 'max-w-7xl mx-auto flex justify-center items-center'
    : isFooter
    ? 'max-w-7xl mx-auto flex justify-center items-center'
    : isSidebar
    ? 'rounded-lg overflow-hidden shadow-sm'
    : '';

  return (
    <div className={`${containerClasses} ${className}`}>
      <div className={adClasses}>
        {ad.type === 'IMAGE' && ad.imageUrl ? (
          ad.linkUrl ? (
            <a
              href={ad.linkUrl}
              target="_blank"
              rel="noopener noreferrer"
              className="block hover:opacity-90 transition-opacity cursor-pointer"
              title={ad.title}
            >
              {isSidebar ? (
                <div className="relative w-full h-auto">
                  <img
                    src={ad.imageUrl}
                    alt={ad.title}
                    className="w-full h-auto object-cover"
                  />
                </div>
              ) : (
                <img
                  src={ad.imageUrl}
                  alt={ad.title}
                  className={`${isHeader ? 'max-h-16' : isFooter ? 'max-h-20' : ''} w-auto h-auto object-contain`}
                />
              )}
            </a>
          ) : (
            <div className="block">
              {isSidebar ? (
                <div className="relative w-full h-auto">
                  <img
                    src={ad.imageUrl}
                    alt={ad.title}
                    className="w-full h-auto object-cover"
                  />
                </div>
              ) : (
                <img
                  src={ad.imageUrl}
                  alt={ad.title}
                  className={`${isHeader ? 'max-h-16' : isFooter ? 'max-h-20' : ''} w-auto h-auto object-contain`}
                />
              )}
            </div>
          )
        ) : ad.type === 'HTML' && ad.htmlCode ? (
          <div
            dangerouslySetInnerHTML={{ __html: ad.htmlCode }}
            className="advertisement-html-content"
          />
        ) : null}
      </div>
    </div>
  );
}

// Sidebar Advertisement component for mall pages with fallback
interface MallSidebarAdvertisementsProps {
  mallId: string;
  className?: string;
}

export function MallSidebarAdvertisements({ mallId, className = '' }: MallSidebarAdvertisementsProps) {
  const [advertisements, setAdvertisements] = useState<Advertisement[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchAdvertisements = async () => {
      try {
        // First, try to get mall-specific sidebar ads
        const params = new URLSearchParams();
        params.set('position', 'MALL_SIDEBAR');
        params.set('mallId', mallId);
        params.set('active', 'true');

        const response = await fetch(`/api/advertisements?${params.toString()}`);
        if (response.ok) {
          const data = await response.json();
          
          // If we have mall-specific ads, use them
          if (data && data.length > 0) {
            setAdvertisements(data);
          } else {
            // Fall back to global sidebar ads
            const fallbackParams = new URLSearchParams();
            fallbackParams.set('position', 'RIGHT_SIDEBAR');
            fallbackParams.set('active', 'true');
            
            const fallbackResponse = await fetch(`/api/advertisements?${fallbackParams.toString()}`);
            if (fallbackResponse.ok) {
              const fallbackData = await fallbackResponse.json();
              setAdvertisements(fallbackData);
            }
          }
        }
      } catch (error) {
        console.error('Error fetching advertisements:', error);
      } finally {
        setLoading(false);
      }
    };

    if (mallId) {
      fetchAdvertisements();
    }
  }, [mallId]);

  // Don't render anything while loading or if no ads
  if (loading || advertisements.length === 0) {
    return null;
  }

  return (
    <div className={`space-y-4 ${className}`}>
      {advertisements.map((ad) => (
        <div key={ad.id} className="rounded-lg overflow-hidden shadow-sm border bg-white dark:bg-slate-900">
          {ad.type === 'IMAGE' && ad.imageUrl ? (
            ad.linkUrl ? (
              <a
                href={ad.linkUrl}
                target="_blank"
                rel="noopener noreferrer"
                className="block hover:opacity-90 transition-opacity cursor-pointer"
                title={ad.title}
              >
                <img
                  src={ad.imageUrl}
                  alt={ad.title}
                  className="w-full h-auto object-cover"
                />
              </a>
            ) : (
              <img
                src={ad.imageUrl}
                alt={ad.title}
                className="w-full h-auto object-cover"
              />
            )
          ) : ad.type === 'HTML' && ad.htmlCode ? (
            <div
              dangerouslySetInnerHTML={{ __html: ad.htmlCode }}
              className="advertisement-html-content p-2"
            />
          ) : null}
        </div>
      ))}
    </div>
  );
}

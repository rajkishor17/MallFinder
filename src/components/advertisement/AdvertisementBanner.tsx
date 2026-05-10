'use client';

import { useEffect, useState } from 'react';
import type { Advertisement, AdPosition } from '@/types/advertisement';

interface AdvertisementBannerProps {
  position: AdPosition;
  mallId?: string;
  className?: string;
  refreshKey?: number | string;
}

export function AdvertisementBanner({ position, mallId, className = '', refreshKey }: AdvertisementBannerProps) {
  const [advertisements, setAdvertisements] = useState<Advertisement[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchAdvertisements = async () => {
      try {
        // Build URL with query params
        const params = new URLSearchParams();
        params.set('position', position);
        params.set('active', 'true');
        if (mallId) {
          params.set('mallId', mallId);
        }
        // Add timestamp to bypass cache
        params.set('_', Date.now().toString());

        const response = await fetch(`/api/advertisements?${params.toString()}`);
        if (response.ok) {
          const data = await response.json();
          setAdvertisements(data);
        }
      } catch (error) {
        console.error('Error fetching advertisements:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchAdvertisements();
  }, [position, mallId, refreshKey]);

  // Don't render anything while loading or if no ads
  if (loading || advertisements.length === 0) {
    return null;
  }

  // Get the first active advertisement for this position
  const ad = advertisements[0];

  if (!ad) {
    return null;
  }

  // Render based on position type
  const isSidebar = position === 'LEFT_SIDEBAR' || position === 'RIGHT_SIDEBAR' || position === 'MALL_SIDEBAR';
  const isHeader = position === 'HEADER' || position === 'MALL_HEADER';
  const isFooter = position === 'FOOTER' || position === 'MALL_FOOTER';

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

// Sidebar Advertisement component with multiple ads support
interface SidebarAdvertisementsProps {
  position: 'LEFT_SIDEBAR' | 'RIGHT_SIDEBAR' | 'MALL_SIDEBAR';
  mallId?: string;
  className?: string;
  refreshKey?: number | string;
}

export function SidebarAdvertisements({ position, mallId, className = '', refreshKey }: SidebarAdvertisementsProps) {
  const [advertisements, setAdvertisements] = useState<Advertisement[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchAdvertisements = async () => {
      try {
        const params = new URLSearchParams();
        params.set('position', position);
        params.set('active', 'true');
        if (mallId) {
          params.set('mallId', mallId);
        }
        // Add timestamp to bypass cache
        params.set('_', Date.now().toString());

        const response = await fetch(`/api/advertisements?${params.toString()}`);
        if (response.ok) {
          const data = await response.json();
          setAdvertisements(data);
        }
      } catch (error) {
        console.error('Error fetching advertisements:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchAdvertisements();
  }, [position, mallId, refreshKey]);

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

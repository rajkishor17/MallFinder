'use client';

import { useState, useEffect } from 'react';
import type { Store } from '@/types/store';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import Image from 'next/image';
import {
  MapPin,
  Phone,
  Globe,
  Clock,
  Building2,
  CheckCircle,
  Hourglass,
  XCircle,
  ExternalLink,
  ChevronLeft,
  ChevronRight,
  Eye,
  Circle,
} from 'lucide-react';
import { motion } from 'framer-motion';

interface StoreCardProps {
  store: Store;
  onClick?: () => void;
  showBorderStatus?: boolean;
  isUpcoming?: boolean;
}

const statusConfig = {
  OPEN: {
    label: 'Open',
    color: 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-800',
    borderColor: 'border-l-emerald-500',
    cardBorder: 'border-emerald-300 dark:border-emerald-700',
    cardBg: 'hover:border-emerald-400 dark:hover:border-emerald-600',
    icon: CheckCircle,
  },
  COMING_SOON: {
    label: 'Coming Soon',
    color: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800',
    borderColor: 'border-l-amber-500',
    cardBorder: 'border-amber-300 dark:border-amber-700',
    cardBg: 'hover:border-amber-400 dark:hover:border-amber-600',
    icon: Hourglass,
  },
  CLOSED: {
    label: 'Closed',
    color: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950 dark:text-red-300 dark:border-red-800',
    borderColor: 'border-l-red-500',
    cardBorder: 'border-red-300 dark:border-red-700',
    cardBg: 'hover:border-red-400 dark:hover:border-red-600',
    icon: XCircle,
  },
};

// Upcoming mall status config (amber colors for all statuses)
const upcomingStatusConfig = {
  OPEN: {
    label: 'Open',
    color: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800',
    borderColor: 'border-l-amber-500',
    cardBorder: 'border-amber-300 dark:border-amber-700',
    cardBg: 'hover:border-amber-400 dark:hover:border-amber-600',
    icon: CheckCircle,
  },
  COMING_SOON: {
    label: 'Coming Soon',
    color: 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800',
    borderColor: 'border-l-amber-500',
    cardBorder: 'border-amber-300 dark:border-amber-700',
    cardBg: 'hover:border-amber-400 dark:hover:border-amber-600',
    icon: Hourglass,
  },
  CLOSED: {
    label: 'Closed',
    color: 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950 dark:text-red-300 dark:border-red-800',
    borderColor: 'border-l-red-500',
    cardBorder: 'border-red-300 dark:border-red-700',
    cardBg: 'hover:border-red-400 dark:hover:border-red-600',
    icon: XCircle,
  },
};

// Function to mask phone number
const maskPhone = (phone: string) => {
  const cleaned = phone.replace(/\D/g, '');
  if (cleaned.length <= 4) return '****';
  const visible = cleaned.slice(-4);
  return `****${visible}`;
};

// Function to mask website URL
const maskWebsite = (url: string) => {
  try {
    const urlObj = new URL(url);
    const domain = urlObj.hostname;
    if (domain.length <= 6) return `${domain.slice(0, 3)}***`;
    return `${domain.slice(0, 6)}***`;
  } catch {
    return '***website***';
  }
};

// Day mapping for parsing
const dayMap: Record<string, number> = {
  'sun': 0, 'sunday': 0,
  'mon': 1, 'monday': 1,
  'tue': 2, 'tues': 2, 'tuesday': 2,
  'wed': 3, 'wednesday': 3,
  'thu': 4, 'thur': 4, 'thurs': 4, 'thursday': 4,
  'fri': 5, 'friday': 5,
  'sat': 6, 'saturday': 6,
};

// Parse time string like "10:00 AM" to minutes since midnight
const parseTime = (timeStr: string): number => {
  const cleaned = timeStr.trim().toUpperCase();
  const match = cleaned.match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/);
  if (!match) return 0;
  
  let hours = parseInt(match[1]);
  const minutes = parseInt(match[2]);
  const period = match[3];
  
  if (period === 'PM' && hours !== 12) hours += 12;
  if (period === 'AM' && hours === 12) hours = 0;
  
  return hours * 60 + minutes;
};

// Check if a day is within a range (handles wrap-around)
const isDayInRange = (day: number, start: number, end: number): boolean => {
  if (start <= end) {
    return day >= start && day <= end;
  } else {
    // Handle wrap-around like Sat-Mon
    return day >= start || day <= end;
  }
};

// Parse opening hours string and check if currently open
const isOpenNow = (openingHours: string): { isOpen: boolean; nextStatus: string } => {
  try {
    const now = new Date();
    const currentDay = now.getDay();
    const currentMinutes = now.getHours() * 60 + now.getMinutes();
    
    // Split by comma to get multiple time ranges
    const ranges = openingHours.split(',').map(r => r.trim());
    
    for (const range of ranges) {
      // Parse patterns like "Mon-Sun: 10:00 AM - 10:00 PM" or "Sun: 11:00 AM - 8:00 PM"
      const match = range.match(/(\w+)(?:-(\w+))?\s*:?\s*(\d{1,2}:\d{2}\s*(?:AM|PM))\s*-\s*(\d{1,2}:\d{2}\s*(?:AM|PM))/i);
      
      if (match) {
        const [, startDayStr, endDayStr, openTimeStr, closeTimeStr] = match;
        
        const startDay = dayMap[startDayStr.toLowerCase()];
        const endDay = endDayStr ? dayMap[endDayStr.toLowerCase()] : startDay;
        
        if (startDay === undefined) continue;
        
        const openMinutes = parseTime(openTimeStr);
        const closeMinutes = parseTime(closeTimeStr);
        
        // Check if current day matches
        if (isDayInRange(currentDay, startDay, endDay === undefined ? startDay : endDay)) {
          // Check if current time is within opening hours
          if (closeMinutes > openMinutes) {
            // Normal case: same day
            if (currentMinutes >= openMinutes && currentMinutes < closeMinutes) {
              const closeTime = closeTimeStr.trim();
              return { isOpen: true, nextStatus: `Closes at ${closeTime}` };
            }
          } else {
            // Handle overnight hours (e.g., 10 PM - 2 AM)
            if (currentMinutes >= openMinutes || currentMinutes < closeMinutes) {
              const closeTime = closeTimeStr.trim();
              return { isOpen: true, nextStatus: `Closes at ${closeTime}` };
            }
          }
        }
      }
    }
    
    return { isOpen: false, nextStatus: 'Currently Closed' };
  } catch {
    return { isOpen: false, nextStatus: '' };
  }
};

export function StoreCard({ store, onClick, showBorderStatus = true, isUpcoming = false }: StoreCardProps) {
  const config = isUpcoming ? upcomingStatusConfig : statusConfig;
  const status = config[store.status];
  const StatusIcon = status.icon;
  const [isDialogOpen, setIsDialogOpen] = useState(false);
  const [currentImageIndex, setCurrentImageIndex] = useState(0);
  const [, setCurrentTime] = useState(0);

  // Update time every minute to refresh open/closed status
  useEffect(() => {
    const interval = setInterval(() => {
      setCurrentTime(prev => prev + 1);
    }, 60000);
    return () => clearInterval(interval);
  }, []);

  // Get current open/closed status based on timing
  const hoursStatus = store.openingHours ? isOpenNow(store.openingHours) : null;

  // Collect all images (main image + additional images) - avoid duplicates
  const allImages = [
    ...(store.imageUrl ? [store.imageUrl] : []),
    ...(store.images?.map(img => img.url).filter(url => url !== store.imageUrl) || [])
  ];

  const hasMultipleImages = allImages.length > 1;

  // Truncate description for card display
  const truncateDescription = (text: string, maxLength: number = 60) => {
    if (text.length <= maxLength) return text;
    return text.slice(0, maxLength).trim() + '...';
  };

  const nextImage = () => {
    setCurrentImageIndex((prev) => (prev + 1) % allImages.length);
  };

  const prevImage = () => {
    setCurrentImageIndex((prev) => (prev - 1 + allImages.length) % allImages.length);
  };

  const handleCardClick = () => {
    setIsDialogOpen(true);
    if (onClick) onClick();
  };

  return (
    <>
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        exit={{ opacity: 0, y: -20 }}
        whileHover={{ scale: 1.02 }}
        transition={{ duration: 0.2 }}
      >
        <Card
          className={`cursor-pointer hover:shadow-lg transition-all duration-300 overflow-hidden border-2 ${status.cardBorder} ${status.cardBg}`}
          onClick={handleCardClick}
        >
          <CardContent className="p-0">
            <div className="flex flex-col">
              {/* Image Section */}
              <div className="w-full h-28 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 flex items-center justify-center relative overflow-hidden">
                {store.imageUrl ? (
                  <Image
                    src={store.imageUrl}
                    alt={store.name}
                    fill
                    className="object-cover"
                    sizes="(max-width: 640px) 100vw, 300px"
                  />
                ) : (
                  <Building2 className="w-10 h-10 text-slate-400" />
                )}
                {/* Image count indicator */}
                {allImages.length > 1 && (
                  <div className="absolute bottom-1 right-1 bg-black/60 text-white text-xs px-1.5 py-0.5 rounded">
                    {allImages.length}
                  </div>
                )}
              </div>

              {/* Content Section - Minimal Info */}
              <div className="flex-1 p-3 min-w-0">
                <div className="flex items-start justify-between gap-2">
                  <div className="min-w-0 flex-1">
                    <h3 className="font-semibold text-sm truncate">{store.name}</h3>
                    <p className="text-xs text-muted-foreground truncate">{store.category}</p>
                  </div>
                  <div className="flex flex-col items-end gap-1">
                    <Badge
                      variant="outline"
                      className={`flex-shrink-0 text-xs ${status.color}`}
                    >
                      <StatusIcon className="w-3 h-3 mr-1" />
                      {status.label}
                    </Badge>
                    {/* Dynamic Open/Closed Status */}
                    {hoursStatus && store.status === 'OPEN' && (
                      <Badge
                        variant="outline"
                        className={`text-xs ${
                          hoursStatus.isOpen
                            ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950 dark:text-green-300 dark:border-green-800'
                            : 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-900 dark:text-gray-400 dark:border-gray-700'
                        }`}
                      >
                        <Circle
                          className={`w-2 h-2 mr-1 ${
                            hoursStatus.isOpen ? 'fill-green-500 text-green-500' : 'fill-gray-400 text-gray-400'
                          }`}
                        />
                        {hoursStatus.isOpen ? 'Open Now' : 'Closed'}
                      </Badge>
                    )}
                  </div>
                </div>

                {/* Truncated Description */}
                {store.description && (
                  <p className="text-xs text-muted-foreground mt-2 line-clamp-2">
                    {truncateDescription(store.description)}
                  </p>
                )}

                {/* Floor info */}
                {store.floor && (
                  <div className="flex items-center gap-1 mt-2 text-xs text-muted-foreground">
                    <MapPin className="w-3 h-3" />
                    <span>{store.floor}{store.unitNumber && ` - ${store.unitNumber}`}</span>
                  </div>
                )}

                {/* Masked Contact Info */}
                <div className="mt-2 space-y-1">
                  {store.phone && (
                    <div className="flex items-center gap-1 text-xs text-muted-foreground">
                      <Phone className="w-3 h-3" />
                      <span className="blur-sm select-none">{maskPhone(store.phone)}</span>
                      <Eye className={`w-3 h-3 ml-1 ${isUpcoming ? 'text-amber-500' : 'text-emerald-500'}`} />
                    </div>
                  )}
                  {store.website && (
                    <div className="flex items-center gap-1 text-xs text-muted-foreground">
                      <Globe className="w-3 h-3" />
                      <span className="blur-sm select-none">{maskWebsite(store.website)}</span>
                      <Eye className={`w-3 h-3 ml-1 ${isUpcoming ? 'text-amber-500' : 'text-emerald-500'}`} />
                    </div>
                  )}
                </div>

                {/* View Details Button */}
                <Button
                  onClick={(e) => {
                    e.stopPropagation();
                    setIsDialogOpen(true);
                  }}
                  className={`w-full mt-3 text-white text-xs h-8 ${isUpcoming ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600' : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'}`}
                >
                  <Eye className="w-3 h-3 mr-1" />
                  View Details
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </motion.div>

      {/* Store Details Dialog */}
      <Dialog open={isDialogOpen} onOpenChange={setIsDialogOpen}>
        <DialogContent className="max-w-lg">
          <DialogHeader>
            <DialogTitle className="text-xl">{store.name}</DialogTitle>
            <DialogDescription className="flex items-center flex-wrap gap-2">
              <Badge variant="outline" className={status.color}>
                <StatusIcon className="w-3 h-3 mr-1" />
                {status.label}
              </Badge>
              {/* Dynamic Open/Closed Status Badge in Popup Header */}
              {hoursStatus && store.status === 'OPEN' && (
                <Badge
                  variant="outline"
                  className={`${
                    hoursStatus.isOpen
                      ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950 dark:text-green-300 dark:border-green-800'
                      : 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-900 dark:text-gray-400 dark:border-gray-700'
                  }`}
                >
                  <Circle
                    className={`w-2 h-2 mr-1 ${
                      hoursStatus.isOpen ? 'fill-green-500 text-green-500 animate-pulse' : 'fill-gray-400 text-gray-400'
                    }`}
                  />
                  {hoursStatus.isOpen ? 'Open Now' : 'Closed'}
                </Badge>
              )}
              <span className="text-muted-foreground">{store.category}</span>
            </DialogDescription>
          </DialogHeader>

          <div className="space-y-4">
            {/* Image Gallery - Always show section */}
            <div className="relative aspect-video bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 rounded-lg overflow-hidden">
              {allImages.length > 0 ? (
                <>
                  <Image
                    src={allImages[currentImageIndex]}
                    alt={`${store.name} image ${currentImageIndex + 1}`}
                    fill
                    className="object-cover"
                    sizes="(max-width: 500px) 100vw, 500px"
                  />

                  {/* Navigation arrows */}
                  {hasMultipleImages && (
                    <>
                      <Button
                        variant="ghost"
                        size="icon"
                        className="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white"
                        onClick={prevImage}
                      >
                        <ChevronLeft className="w-4 h-4" />
                      </Button>
                      <Button
                        variant="ghost"
                        size="icon"
                        className="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white"
                        onClick={nextImage}
                      >
                        <ChevronRight className="w-4 h-4" />
                      </Button>

                      {/* Image indicators */}
                      <div className="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
                        {allImages.map((_, idx) => (
                          <button
                            key={idx}
                            onClick={() => setCurrentImageIndex(idx)}
                            className={`w-2 h-2 rounded-full transition-colors ${
                              idx === currentImageIndex ? 'bg-white' : 'bg-white/40'
                            }`}
                          />
                        ))}
                      </div>
                    </>
                  )}
                </>
              ) : (
                /* Placeholder when no images */
                <div className="absolute inset-0 flex flex-col items-center justify-center text-muted-foreground">
                  <Building2 className="w-16 h-16 mb-2 opacity-50" />
                  <p className="text-sm">No images available</p>
                </div>
              )}
            </div>

            {/* Description */}
            {store.description && (
              <div>
                <h4 className="text-sm font-medium mb-1">About</h4>
                <p className="text-sm text-muted-foreground">{store.description}</p>
              </div>
            )}

            {/* Store Details - Revealed in Popup */}
            <div className="space-y-3">
              {store.floor && (
                <div className="flex items-center gap-2 text-sm">
                  <MapPin className="w-4 h-4 text-muted-foreground" />
                  <span>{store.floor}{store.unitNumber && ` - ${store.unitNumber}`}</span>
                </div>
              )}
              {store.openingHours && (
                <div className="space-y-2">
                  <div className="flex items-center gap-2 text-sm">
                    <Clock className="w-4 h-4 text-muted-foreground" />
                    <span>{store.openingHours}</span>
                  </div>
                  {/* Dynamic Open/Closed Status in Popup */}
                  {hoursStatus && store.status === 'OPEN' && (
                    <div className="flex items-center gap-2 ml-6">
                      <Circle
                        className={`w-2.5 h-2.5 ${
                          hoursStatus.isOpen
                            ? 'fill-green-500 text-green-500 animate-pulse'
                            : 'fill-gray-400 text-gray-400'
                        }`}
                      />
                      <span className={`text-sm font-medium ${
                        hoursStatus.isOpen ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'
                      }`}>
                        {hoursStatus.isOpen ? 'Open Now' : 'Currently Closed'}
                      </span>
                      {hoursStatus.isOpen && hoursStatus.nextStatus && (
                        <span className="text-xs text-muted-foreground">
                          • {hoursStatus.nextStatus}
                        </span>
                      )}
                    </div>
                  )}
                </div>
              )}
            </div>

            {/* Action Buttons */}
            <div className="flex flex-col sm:flex-row gap-2 pt-2">
              {store.phone && (
                <Button
                  asChild
                  className={`flex-1 text-white ${isUpcoming ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600' : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'}`}
                >
                  <a href={`tel:${store.phone}`}>
                    <Phone className="w-4 h-4 mr-2" />
                    Call Now
                  </a>
                </Button>
              )}
              {store.website && (
                <Button
                  asChild
                  variant="outline"
                  className={`flex-1 ${isUpcoming ? 'border-amber-500 text-amber-600 hover:bg-amber-50 dark:border-amber-400 dark:text-amber-400 dark:hover:bg-amber-950' : 'border-emerald-500 text-emerald-600 hover:bg-emerald-50 dark:border-emerald-400 dark:text-emerald-400 dark:hover:bg-emerald-950'}`}
                >
                  <a
                    href={store.website}
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <Globe className="w-4 h-4 mr-2" />
                    Visit Website
                    <ExternalLink className="w-3 h-3 ml-2" />
                  </a>
                </Button>
              )}
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </>
  );
}

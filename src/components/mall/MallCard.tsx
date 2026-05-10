'use client';

import { Mall } from '@/types/mall';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { MapPin, Clock, Calendar, Store, Sparkles, Eye } from 'lucide-react';
import { motion } from 'framer-motion';

interface MallCardProps {
  mall: Mall;
  isSelected?: boolean;
  onClick?: () => void;
  showViewButton?: boolean;
}

export function MallCard({ mall, isSelected = false, onClick, showViewButton = false }: MallCardProps) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3 }}
      whileHover={{ scale: 1.02 }}
      whileTap={{ scale: 0.98 }}
    >
      <Card
        className={`overflow-hidden cursor-pointer transition-all duration-300 border-2 ${
          isSelected
            ? 'ring-2 ring-emerald-500 shadow-lg'
            : 'hover:shadow-md'
        } ${mall.category === 'upcoming' ? 'border-amber-300 hover:border-amber-400' : 'border-emerald-300 hover:border-emerald-400'}`}
        onClick={onClick}
      >
        <div className="relative h-40 overflow-hidden">
          <img
            src={mall.imageUrl || '/placeholder-mall.jpg'}
            alt={mall.name}
            className="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
          <div className="absolute bottom-3 left-3 right-3">
            <Badge
              variant={mall.category === 'existing' ? 'default' : 'secondary'}
              className={`mb-2 ${
                mall.category === 'existing'
                  ? 'bg-emerald-500 hover:bg-emerald-600'
                  : 'bg-amber-500 hover:bg-amber-600 text-white'
              }`}
            >
              {mall.category === 'existing' ? (
                <span className="flex items-center gap-1">
                  <Store className="w-3 h-3" />
                  Existing Mall
                </span>
              ) : (
                <span className="flex items-center gap-1">
                  <Sparkles className="w-3 h-3" />
                  Upcoming Mall
                </span>
              )}
            </Badge>
            <h3 className="text-lg font-semibold text-white truncate">{mall.name}</h3>
          </div>
        </div>
        <CardContent className="p-4">
          <div className="flex items-start gap-2 text-sm text-muted-foreground mb-3">
            <MapPin className="w-4 h-4 mt-0.5 shrink-0" />
            <span className="line-clamp-2">{mall.address}</span>
          </div>
          
          <p className="text-sm text-muted-foreground line-clamp-2 mb-3">
            {mall.description}
          </p>

          {mall.category === 'existing' && mall.openingHours && (
            <div className="flex items-center gap-2 text-sm text-muted-foreground mb-3">
              <Clock className="w-4 h-4 shrink-0" />
              <span className="truncate">{mall.openingHours}</span>
            </div>
          )}

          {mall.category === 'upcoming' && mall.expectedOpeningDate && (
            <div className="flex items-center gap-2 text-sm text-amber-600 mb-3">
              <Calendar className="w-4 h-4 shrink-0" />
              <span className="truncate">Expected: {mall.expectedOpeningDate}</span>
            </div>
          )}

          <div className="flex items-center gap-2 text-sm font-medium mb-3">
            <Store className="w-4 h-4 shrink-0" />
            <span>{mall.storesCount ?? 0} Stores</span>
          </div>

          <div className="flex flex-wrap gap-1 mb-3">
            {mall.features.slice(0, 3).map((feature, index) => (
              <Badge
                key={index}
                variant="outline"
                className="text-xs"
              >
                {feature}
              </Badge>
            ))}
            {mall.features.length > 3 && (
              <Badge variant="outline" className="text-xs">
                +{mall.features.length - 3} more
              </Badge>
            )}
          </div>

          {showViewButton && (
            <Button
              size="sm"
              className={`w-full text-white ${
                mall.category === 'upcoming'
                  ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600'
                  : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
              }`}
              onClick={(e) => {
                e.stopPropagation();
                onClick();
              }}
            >
              <Eye className="w-4 h-4 mr-2" />
              View Details
            </Button>
          )}
        </CardContent>
      </Card>
    </motion.div>
  );
}

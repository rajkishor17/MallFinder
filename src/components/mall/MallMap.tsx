'use client';

import { useEffect, useRef, useState } from 'react';
import { useRouter } from 'next/navigation';
import { MapContainer, TileLayer, Marker, Popup, useMap } from 'react-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { Mall } from '@/types/mall';

// Fix for default marker icons in Leaflet with Next.js
const createIcon = (isUpcoming: boolean) => {
  const color = isUpcoming ? '#f59e0b' : '#10b981';
  const svgIcon = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${color}" width="32" height="32">
      <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
    </svg>
  `;
  return L.divIcon({
    html: svgIcon,
    className: 'custom-marker',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });
};

interface MallMapProps {
  malls: Mall[];
  selectedMall: Mall | null;
  onMallSelect: (mall: Mall) => void;
}

// Marker component with controlled popup
function MallMarker({ 
  mall, 
  isSelected, 
  onSelect, 
  onPopupOpen 
}: { 
  mall: Mall; 
  isSelected: boolean; 
  onSelect: (mall: Mall) => void;
  onPopupOpen: (mall: Mall) => void;
}) {
  const map = useMap();
  const markerRef = useRef<L.Marker>(null);
  const hasOpenedRef = useRef(false);

  useEffect(() => {
    if (isSelected && markerRef.current && !hasOpenedRef.current) {
      hasOpenedRef.current = true;
      
      // Get the marker's position
      const latLng = markerRef.current.getLatLng();
      
      // Calculate offset to position popup in center of visible area
      // Get the map container size
      const container = map.getContainer();
      const containerHeight = container.clientHeight;
      
      // We want the popup to appear in the middle of the visible area
      // Popup appears above the marker, so we need to position the map
      // such that the marker is in the lower half of the screen
      
      // Convert the desired marker screen position to map coordinates
      // We want marker at ~70% from top (to leave room for popup above)
      const targetMarkerScreenY = containerHeight * 0.7;
      const centerX = container.clientWidth / 2;
      
      // Get the current pixel position of the marker
      const markerPoint = map.latLngToContainerPoint(latLng);
      
      // Calculate how much to pan
      const panX = markerPoint.x - centerX;
      const panY = markerPoint.y - targetMarkerScreenY;
      
      // Get new center
      const currentCenter = map.getCenter();
      const currentCenterPoint = map.latLngToContainerPoint(currentCenter);
      const newCenterPoint = L.point(
        currentCenterPoint.x + panX,
        currentCenterPoint.y + panY
      );
      const newCenter = map.containerPointToLatLng(newCenterPoint);
      
      // Pan to new center, then open popup
      map.once('moveend', () => {
        setTimeout(() => {
          markerRef.current?.openPopup();
          onPopupOpen(mall);
        }, 50);
      });
      
      map.panTo(newCenter, { animate: true, duration: 0.4 });
    }
    
    if (!isSelected) {
      hasOpenedRef.current = false;
    }
  }, [isSelected, mall, map, onPopupOpen]);

  const router = useRouter();

  return (
    <Marker
      ref={markerRef}
      position={[mall.coordinates.lat, mall.coordinates.lng]}
      icon={createIcon(mall.category === 'upcoming')}
      eventHandlers={{
        click: () => onSelect(mall),
      }}
    >
      <Popup
        maxWidth={280}
        minWidth={220}
        closeOnClick={false}
      >
        <div className="min-w-[200px]">
          <h3 className="font-semibold text-base">{mall.name}</h3>
          <p className="text-sm text-muted-foreground mt-1">{mall.address}</p>
          <div className="mt-2 flex items-center gap-2">
            <span className={`inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${
              mall.category === 'existing'
                ? 'bg-emerald-100 text-emerald-800'
                : 'bg-amber-100 text-amber-800'
            }`}>
              {mall.category === 'existing' ? 'Existing Mall' : 'Upcoming Mall'}
            </span>
          </div>
          {mall.category === 'existing' && mall.openingHours && (
            <p className="text-xs text-muted-foreground mt-1">{mall.openingHours}</p>
          )}
          {mall.category === 'upcoming' && mall.expectedOpeningDate && (
            <p className="text-xs text-muted-foreground mt-1">
              Expected: {mall.expectedOpeningDate}
            </p>
          )}
          <button
            onClick={() => router.push(`/mall/${mall.id}`)}
            className={`mt-3 w-full px-3 py-1.5 text-white text-sm font-medium rounded-md transition-all shadow-sm ${
              mall.category === 'upcoming'
                ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600'
                : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
            }`}
          >
            View Details
          </button>
        </div>
      </Popup>
    </Marker>
  );
}

export function MallMap({ malls, selectedMall, onMallSelect }: MallMapProps) {
  const [popupOpenedFor, setPopupOpenedFor] = useState<string | null>(null);

  const handlePopupOpen = (mall: Mall) => {
    setPopupOpenedFor(mall.id);
  };

  return (
    <MapContainer
      center={[20.5937, 78.9629]}
      zoom={5}
      className="w-full h-full rounded-lg z-0"
      style={{ background: '#f8fafc' }}
    >
      <TileLayer
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />
      {malls.map((mall) => (
        <MallMarker
          key={mall.id}
          mall={mall}
          isSelected={selectedMall?.id === mall.id && popupOpenedFor !== mall.id}
          onSelect={onMallSelect}
          onPopupOpen={handlePopupOpen}
        />
      ))}
    </MapContainer>
  );
}

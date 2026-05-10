'use client';

import { useEffect, useCallback } from 'react';
import { MapContainer, TileLayer, Marker, useMap, useMapEvents } from 'react-leaflet';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Fix for default marker icons in Leaflet with Next.js
const createIcon = () => {
  const svgIcon = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#10b981" width="32" height="32">
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

interface CoordinatePickerMapProps {
  lat: number;
  lng: number;
  name?: string;
  onCoordinatesChange: (lat: number, lng: number) => void;
}

// Component to handle map view changes
function MapController({ lat, lng }: { lat: number; lng: number }) {
  const map = useMap();

  useEffect(() => {
    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
      map.flyTo([lat, lng], 15, {
        duration: 0.5,
      });
    }
  }, [lat, lng, map]);

  return null;
}

// Component to handle click events on map
function MapClickHandler({ onCoordinatesChange }: { onCoordinatesChange: (lat: number, lng: number) => void }) {
  useMapEvents({
    click: (e) => {
      const { lat, lng } = e.latlng;
      onCoordinatesChange(lat, lng);
    },
  });
  return null;
}

// Component for draggable marker
function DraggableMarker({ 
  lat, 
  lng, 
  name, 
  onCoordinatesChange 
}: { 
  lat: number; 
  lng: number; 
  name?: string;
  onCoordinatesChange: (lat: number, lng: number) => void;
}) {
  const map = useMap();

  const eventHandlers = {
    dragend: useCallback((e: L.DragEndEvent) => {
      const marker = e.target as L.Marker;
      const position = marker.getLatLng();
      onCoordinatesChange(position.lat, position.lng);
    }, [onCoordinatesChange]),
  };

  // Update marker position when coordinates change
  useEffect(() => {
    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
      map.setView([lat, lng], map.getZoom());
    }
  }, [lat, lng, map]);

  return (
    <Marker
      position={[lat, lng]}
      icon={createIcon()}
      draggable={true}
      eventHandlers={eventHandlers}
    />
  );
}

export function CoordinatePickerMap({ lat, lng, name, onCoordinatesChange }: CoordinatePickerMapProps) {
  // Default to a central location if coordinates are not set
  const defaultLat = 40.7580;
  const defaultLng = -73.9855;
  
  const safeLat = lat && !isNaN(lat) ? lat : defaultLat;
  const safeLng = lng && !isNaN(lng) ? lng : defaultLng;

  return (
    <MapContainer
      center={[safeLat, safeLng]}
      zoom={13}
      className="w-full h-full rounded-lg z-0"
      style={{ background: '#f8fafc' }}
    >
      <TileLayer
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />
      <MapController lat={safeLat} lng={safeLng} />
      <MapClickHandler onCoordinatesChange={onCoordinatesChange} />
      <DraggableMarker 
        lat={safeLat} 
        lng={safeLng} 
        name={name} 
        onCoordinatesChange={onCoordinatesChange} 
      />
    </MapContainer>
  );
}

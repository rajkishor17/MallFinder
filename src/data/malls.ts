import { Mall } from '@/types/mall';

export const malls: Mall[] = [
  // Existing Malls
  {
    id: 'mall-1',
    name: 'Grand City Mall',
    address: '123 Central Avenue, Downtown District',
    description: 'A premier shopping destination featuring over 200 retail stores, luxury boutiques, and world-class dining options. The mall boasts stunning architecture with a glass atrium and spacious walkways.',
    category: 'existing',
    coordinates: { lat: 40.7580, lng: -73.9855 },
    features: ['Luxury Brands', 'Food Court', 'Cinema Complex', 'Kids Play Area'],
    storesCount: 220,
    amenities: ['Valet Parking', 'Free WiFi', 'ATM', 'Baby Care Rooms', 'Wheelchair Access'],
    openingHours: 'Mon-Sun: 10:00 AM - 10:00 PM',
    imageUrl: '/malls/grand-city-mall.png',
    area: 'Downtown'
  },
  {
    id: 'mall-2',
    name: 'Metro Plaza',
    address: '456 Metro Boulevard, Midtown',
    description: 'Metro Plaza offers a modern shopping experience with a perfect blend of fashion, electronics, and lifestyle stores. Features an indoor amusement park and rooftop garden.',
    category: 'existing',
    coordinates: { lat: 40.7614, lng: -73.9776 },
    features: ['Indoor Amusement Park', 'Rooftop Garden', 'Tech Hub', 'Art Gallery'],
    storesCount: 180,
    amenities: ['Multi-level Parking', 'Restaurant Terrace', 'Concierge Service', 'Gift Wrapping'],
    openingHours: 'Mon-Thu: 10:00 AM - 9:00 PM, Fri-Sun: 10:00 AM - 11:00 PM',
    imageUrl: '/malls/metro-plaza.png',
    area: 'Midtown'
  },
  {
    id: 'mall-3',
    name: 'Harbor Shopping Center',
    address: '789 Waterfront Road, Harbor District',
    description: 'Experience waterfront shopping at its finest. Harbor Shopping Center combines retail therapy with scenic harbor views, featuring boutique stores and waterfront dining.',
    category: 'existing',
    coordinates: { lat: 40.6892, lng: -74.0445 },
    features: ['Waterfront Views', 'Seafood Restaurants', 'Boutique Stores', 'Boat Tours'],
    storesCount: 95,
    amenities: ['Boat Docking', 'Outdoor Seating', 'Seafood Market', 'Photo Spots'],
    openingHours: 'Mon-Sun: 9:00 AM - 9:00 PM',
    imageUrl: '/malls/harbor-shopping.png',
    area: 'Harbor District'
  },
  {
    id: 'mall-4',
    name: 'Riverside Mall',
    address: '321 River Walk, East Side',
    description: 'Riverside Mall is a family-friendly shopping destination with a focus on affordable fashion and entertainment. Home to the largest indoor ice rink in the city.',
    category: 'existing',
    coordinates: { lat: 40.7484, lng: -73.9857 },
    features: ['Ice Skating Rink', 'Family Entertainment', 'Outlet Stores', 'Event Hall'],
    storesCount: 150,
    amenities: ['Ice Skate Rental', 'Party Rooms', 'Lost & Found', 'First Aid Station'],
    openingHours: 'Mon-Sat: 10:00 AM - 10:00 PM, Sun: 11:00 AM - 8:00 PM',
    imageUrl: '/malls/riverside-mall.png',
    area: 'East Side'
  },
  // Upcoming Malls
  {
    id: 'mall-5',
    name: 'Sky Tower Mall',
    address: '555 Cloud Street, Financial District',
    description: 'Set to become the city\'s tallest shopping complex, Sky Tower Mall will feature innovative vertical retail spaces, sky bridges connecting towers, and an observation deck.',
    category: 'upcoming',
    coordinates: { lat: 40.7074, lng: -74.0113 },
    features: ['Vertical Shopping', 'Sky Bridges', 'Observation Deck', 'Helipad Access'],
    storesCount: 300,
    amenities: ['Express Elevators', 'Sky Lounge', 'Private Suites', 'Rooftop Helipad'],
    expectedOpeningDate: 'June 2025',
    imageUrl: '/malls/sky-tower.png',
    area: 'Financial District'
  },
  {
    id: 'mall-6',
    name: 'Eco Garden Mall',
    address: '777 Green Valley Road, Nature District',
    description: 'A revolutionary eco-friendly mall concept featuring indoor gardens, sustainable architecture, and green technology. Will be the first carbon-neutral shopping center in the region.',
    category: 'upcoming',
    coordinates: { lat: 40.7829, lng: -73.9654 },
    features: ['Indoor Gardens', 'Solar Powered', 'Green Roof', 'Eco Stores'],
    storesCount: 120,
    amenities: ['Electric Vehicle Charging', 'Bicycle Parking', 'Organic Food Court', 'Nature Trail'],
    expectedOpeningDate: 'September 2025',
    imageUrl: '/malls/eco-garden.png',
    area: 'Nature District'
  },
  {
    id: 'mall-7',
    name: 'Tech Hub Mall',
    address: '888 Innovation Drive, Tech Valley',
    description: 'The future of tech shopping arrives with Tech Hub Mall. Featuring VR experience zones, robot-assisted services, and the latest in consumer technology.',
    category: 'upcoming',
    coordinates: { lat: 40.7549, lng: -73.9840 },
    features: ['VR Experience Zones', 'Robot Services', 'Tech Showrooms', 'Innovation Lab'],
    storesCount: 80,
    amenities: ['Smart Parking', 'AR Navigation', 'Tech Support Hub', 'Drone Delivery'],
    expectedOpeningDate: 'March 2025',
    imageUrl: '/malls/tech-hub.png',
    area: 'Tech Valley'
  },
  {
    id: 'mall-8',
    name: 'Lifestyle Village',
    address: '999 Community Lane, Suburban West',
    description: 'A community-centered lifestyle destination combining shopping, dining, and wellness. Will feature an open-air design with pedestrian-friendly walkways and community spaces.',
    category: 'upcoming',
    coordinates: { lat: 40.7282, lng: -73.7949 },
    features: ['Open-Air Design', 'Wellness Center', 'Community Space', 'Farmers Market'],
    storesCount: 140,
    amenities: ['Pet-Friendly Zones', 'Outdoor Yoga', 'Community Events', 'Sustainable Practices'],
    expectedOpeningDate: 'December 2025',
    imageUrl: '/malls/lifestyle-village.png',
    area: 'Suburban West'
  }
];

export const existingMalls = malls.filter(mall => mall.category === 'existing');
export const upcomingMalls = malls.filter(mall => mall.category === 'upcoming');

export const getMallById = (id: string): Mall | undefined => {
  return malls.find(mall => mall.id === id);
};

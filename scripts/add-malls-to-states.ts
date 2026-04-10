import { PrismaClient } from '@prisma/client';

const prisma = new PrismaClient();

// Mall data for each state
const mallData: Record<string, { name: string; city: string; area: string; category: 'existing' | 'upcoming' }> = {
  'Andhra Pradesh': { name: 'Vijayawada Central Mall', city: 'Vijayawada', area: 'MG Road', category: 'existing' },
  'Arunachal Pradesh': { name: 'Itanagar Shopping Plaza', city: 'Itanagar', area: 'Ganga Market', category: 'upcoming' },
  'Assam': { name: 'Guwahati City Centre', city: 'Guwahati', area: 'Paltan Bazaar', category: 'existing' },
  'Bihar': { name: 'Patna Central Mall', city: 'Patna', area: 'Boring Road', category: 'existing' },
  'Chattisgarh': { name: 'Raipur City Mall', city: 'Raipur', area: 'Civil Lines', category: 'existing' },
  'Goa': { name: 'Panaji Beachside Mall', city: 'Panaji', area: 'Fontainhas', category: 'existing' },
  'Gujarat': { name: 'Ahmedabad One Mall', city: 'Ahmedabad', area: 'CG Road', category: 'existing' },
  'Haryana': { name: 'Gurgaon Metropolitan Mall', city: 'Gurugram', area: 'MG Road', category: 'existing' },
  'Himachal Pradesh': { name: 'Shimla Ridge Mall', city: 'Shimla', area: 'Mall Road', category: 'upcoming' },
  'Jharkhand': { name: 'Ranchi City Center', city: 'Ranchi', area: 'Main Road', category: 'existing' },
  'Karnataka': { name: 'Bangalore Phoenix Mall', city: 'Bengaluru', area: 'Whitefield', category: 'existing' },
  'Kerala': { name: 'Kochi Lulu Mall', city: 'Kochi', area: 'Edappally', category: 'existing' },
  'Madhya Pradesh': { name: 'Bhopal DB City Mall', city: 'Bhopal', area: 'MP Nagar', category: 'existing' },
  'Maharashtra': { name: 'Phoenix Marketcity Pune', city: 'Pune', area: 'Viman Nagar', category: 'existing' },
  'Manipur': { name: 'Imphal City Mall', city: 'Imphal', area: 'Paona Bazaar', category: 'upcoming' },
  'Meghalaya': { name: 'Shillong Central Plaza', city: 'Shillong', area: 'Police Bazaar', category: 'existing' },
  'Mizoram': { name: 'Aizawl Mall Complex', city: 'Aizawl', area: 'Bara Bazaar', category: 'upcoming' },
  'Nagaland': { name: 'Dimapur Shopping Hub', city: 'Dimapur', area: 'Supermarket Area', category: 'existing' },
  'Odisha': { name: 'Bhubaneswar Esplanade Mall', city: 'Bhubaneswar', area: 'Rasulgarh', category: 'existing' },
  'Punjab': { name: 'Amritsar Celebration Mall', city: 'Amritsar', area: 'GT Road', category: 'existing' },
  'Rajasthan': { name: 'Jaipur World Trade Park', city: 'Jaipur', area: 'Malviya Nagar', category: 'existing' },
  'Sikkim': { name: 'Gangtok Mall Road Plaza', city: 'Gangtok', area: 'MG Marg', category: 'upcoming' },
  'Tamil Nadu': { name: 'Chennai Express Avenue', city: 'Chennai', area: 'Royapettah', category: 'existing' },
  'Telangana': { name: 'Hyderabad Inorbit Mall', city: 'Hyderabad', area: 'Hitech City', category: 'existing' },
  'Tripura': { name: 'Agartala City Centre', city: 'Agartala', area: 'Kunjaban', category: 'upcoming' },
  'Uttar Pradesh': { name: 'Lucknow Sahara Ganj Mall', city: 'Lucknow', area: 'Gomti Nagar', category: 'existing' },
  'Uttarakhand': { name: 'Dehradun Pacific Mall', city: 'Dehradun', area: 'Rajpur Road', category: 'existing' },
  'West Bengal': { name: 'Kolkata South City Mall', city: 'Kolkata', area: 'Jadavpur', category: 'existing' },
};

const mallDescriptions: Record<string, string> = {
  existing: 'A premier shopping destination featuring top retail brands, food courts, and entertainment zones. Perfect for family outings with modern amenities and ample parking.',
  upcoming: 'An upcoming world-class shopping complex featuring international brands, state-of-the-art facilities, and unique architectural design. Coming soon to serve the community.'
};

const features = [
  ['Multi-level Parking', 'Food Court', 'Cinema', 'Kids Zone'],
  ['Valet Parking', 'Fine Dining', 'Gaming Zone', 'Spa'],
  ['Luxury Brands', 'ATM', 'WiFi', 'Baby Care Rooms'],
  ['Indoor Entertainment', 'Restaurant Terrace', 'Event Space', 'Fitness Center'],
];

const amenities = [
  ['Free WiFi', 'ATM', 'First Aid', 'Wheelchair Access'],
  ['Valet Parking', 'Lost & Found', 'Restrooms', 'Security'],
  ['Car Charging', 'Baby Rooms', 'Prayer Room', 'Lockers'],
];

async function main() {
  console.log('Starting to add malls to states...');
  
  // Get all states
  const states = await prisma.state.findMany();
  console.log(`Found ${states.length} states`);
  
  for (const state of states) {
    const data = mallData[state.name];
    if (!data) {
      console.log(`No mall data for state: ${state.name}`);
      continue;
    }
    
    // Check if mall already exists for this state
    const existingMall = await prisma.mall.findFirst({
      where: { state: state.name }
    });
    
    if (existingMall) {
      console.log(`Mall already exists for state: ${state.name}`);
      continue;
    }
    
    // Create or get city
    let city = await prisma.city.findFirst({
      where: { name: data.city, stateId: state.id }
    });
    
    if (!city) {
      city = await prisma.city.create({
        data: { name: data.city, stateId: state.id }
      });
      console.log(`Created city: ${data.city} for state: ${state.name}`);
    }
    
    // Create or get area
    let area = await prisma.area.findFirst({
      where: { name: data.area, cityId: city.id }
    });
    
    if (!area) {
      area = await prisma.area.create({
        data: { name: data.area, cityId: city.id }
      });
      console.log(`Created area: ${data.area} for city: ${data.city}`);
    }
    
    // Create mall
    const randomFeatures = features[Math.floor(Math.random() * features.length)];
    const randomAmenities = amenities[Math.floor(Math.random() * amenities.length)];
    
    const mall = await prisma.mall.create({
      data: {
        name: data.name,
        description: mallDescriptions[data.category],
        address: `${Math.floor(Math.random() * 900) + 100} ${data.area} Main Road`,
        area: data.area,
        city: data.city,
        state: state.name,
        latitude: 20.0 + Math.random() * 10,
        longitude: 70.0 + Math.random() * 15,
        status: data.category === 'existing' ? 'EXISTING' : 'UPCOMING',
        openingHours: data.category === 'existing' ? 'Mon-Sun: 10:00 AM - 10:00 PM' : undefined,
        expectedOpeningDate: data.category === 'upcoming' ? 'December 2025' : undefined,
        features: JSON.stringify(randomFeatures),
        amenities: JSON.stringify(randomAmenities),
        storesCount: Math.floor(Math.random() * 50) + 20,
      }
    });
    
    console.log(`Created mall: ${data.name} in ${state.name}`);
  }
  
  console.log('Finished adding malls!');
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });

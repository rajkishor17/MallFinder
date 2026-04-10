'use client';

import { useState, useEffect, useCallback, useRef } from 'react';
import dynamic from 'next/dynamic';
import Link from 'next/link';
import Image from 'next/image';
import { useRouter } from 'next/navigation';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { MallCard } from '@/components/mall/MallCard';
import { AdvertisementBanner, SidebarAdvertisements } from '@/components/advertisement/AdvertisementBanner';
import { SocialLinks, SocialLinksSkeleton } from '@/components/ui/social-links';
import { SocialShareButtons } from '@/components/ui/social-share-buttons';
import { SiteFooter } from '@/components/layout/SiteFooter';
import {
  Search,
  Store,
  Sparkles,
  MapPin,
  Building2,
  Loader2,
  Menu,
  X,
  ChevronLeft,
  ChevronRight,
  Filter,
  RefreshCw,
} from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { Mall } from '@/types/mall';
import type { Area } from '@/types/area';

// Types for location hierarchy
interface State {
  id: string;
  name: string;
  cities?: City[];
}

interface City {
  id: string;
  name: string;
  stateId: string;
  areas?: Area[];
}

// Dynamic import for MallMap
const MallMap = dynamic(
  () => import('@/components/mall/MallMap').then((mod) => mod.MallMap),
  {
    ssr: false,
    loading: () => (
      <div className="w-full h-full bg-muted rounded-lg flex items-center justify-center">
        <div className="text-muted-foreground">Loading map...</div>
      </div>
    ),
  }
);

interface SiteSettings {
  siteName: string;
  siteTagline: string;
  siteLogo: string;
  facebook?: string;
  twitter?: string;
  instagram?: string;
  linkedin?: string;
  youtube?: string;
  whatsapp?: string;
}

export default function PublicHomePage() {
  const router = useRouter();
  const [malls, setMalls] = useState<Mall[]>([]);
  const [areas, setAreas] = useState<Area[]>([]);
  const [states, setStates] = useState<State[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [selectedMall, setSelectedMall] = useState<Mall | null>(null);
  const [showMap, setShowMap] = useState(true);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const MALLS_PER_PAGE = 12;
  const exploreCarouselRef = useRef<HTMLDivElement>(null);
  const searchContainerRef = useRef<HTMLDivElement>(null);
  const searchContainerRef2 = useRef<HTMLDivElement>(null);
  const [siteSettings, setSiteSettings] = useState<SiteSettings>({
    siteName: 'MallFinder',
    siteTagline: 'Discover Shopping Destinations',
    siteLogo: '',
    facebook: '',
    twitter: '',
    instagram: '',
    linkedin: '',
    youtube: '',
    whatsapp: '',
  });
  
  // Map location filters
  const [mapStateFilter, setMapStateFilter] = useState<string>('all');
  const [mapCityFilter, setMapCityFilter] = useState<string>('all');
  const [mapAreaFilter, setMapAreaFilter] = useState<string>('all');
  
  // Search suggestions
  const [showSuggestions, setShowSuggestions] = useState(false);
  const [showSuggestions2, setShowSuggestions2] = useState(false);

  // Fetch malls from API
  const fetchMalls = useCallback(async () => {
    try {
      setLoading(true);
      const response = await fetch('/api/malls');
      if (!response.ok) throw new Error('Failed to fetch malls');
      const data = await response.json();
      setMalls(data);
    } catch {
      console.error('Failed to load malls');
    } finally {
      setLoading(false);
    }
  }, []);

  // Fetch areas from API
  const fetchAreas = useCallback(async () => {
    try {
      const response = await fetch('/api/areas');
      if (!response.ok) throw new Error('Failed to fetch areas');
      const data = await response.json();
      setAreas(data);
    } catch {
      console.error('Failed to load areas');
    }
  }, []);

  // Fetch site settings
  const fetchSettings = useCallback(async () => {
    try {
      const response = await fetch('/api/settings');
      if (response.ok) {
        const data = await response.json();
        setSiteSettings({
          siteName: data.siteName || 'MallFinder',
          siteTagline: data.siteTagline || 'Discover Shopping Destinations',
          siteLogo: data.siteLogo || '',
          facebook: data.facebook || '',
          twitter: data.twitter || '',
          instagram: data.instagram || '',
          linkedin: data.linkedin || '',
          youtube: data.youtube || '',
          whatsapp: data.whatsapp || '',
        });
      }
    } catch {
      console.error('Failed to load settings');
    }
  }, []);

  // Fetch states from API
  const fetchStates = useCallback(async () => {
    try {
      const response = await fetch('/api/states');
      if (!response.ok) throw new Error('Failed to fetch states');
      const data = await response.json();
      setStates(data);
    } catch {
      console.error('Failed to load states');
    }
  }, []);

  useEffect(() => {
    fetchMalls();
    fetchAreas();
    fetchSettings();
    fetchStates();
  }, [fetchMalls, fetchAreas, fetchSettings, fetchStates]);
  
  // Get cities for selected state (for map filter)
  const citiesForMapState = states.find(s => s.id === mapStateFilter || s.name === mapStateFilter)?.cities || [];
  
  // Get areas for selected city (for map filter)
  const areasForMapCity = citiesForMapState.find(c => c.id === mapCityFilter || c.name === mapCityFilter)?.areas || [];
  
  // Reset city and area when state changes
  useEffect(() => {
    setMapCityFilter('all');
    setMapAreaFilter('all');
  }, [mapStateFilter]);
  
  // Reset area when city changes
  useEffect(() => {
    setMapAreaFilter('all');
  }, [mapCityFilter]);

  // Filter malls based on search and status
  const filteredMalls = malls.filter((mall) => {
    const query = searchQuery.toLowerCase();
    const matchesSearch =
      mall.name.toLowerCase().includes(query) ||
      mall.address.toLowerCase().includes(query) ||
      mall.area.toLowerCase().includes(query) ||
      (mall.city?.toLowerCase().includes(query) ?? false) ||
      (mall.state?.toLowerCase().includes(query) ?? false);
    const matchesStatus =
      statusFilter === 'all' || mall.category === statusFilter;
    return matchesSearch && matchesStatus;
  });

  // Statistics
  const totalMalls = malls.length;
  const existingMalls = malls.filter((m) => m.category === 'existing').length;
  const upcomingMalls = malls.filter((m) => m.category === 'upcoming').length;
  
  // Filtered counts for display
  const filteredExistingCount = filteredMalls.filter((m) => m.category === 'existing').length;
  const filteredUpcomingCount = filteredMalls.filter((m) => m.category === 'upcoming').length;

  // Areas for filter
  const areaNames = areas.length > 0
    ? areas.map(a => a.name)
    : [...new Set(malls.map((m) => m.area))].filter(Boolean);

  // Malls for map - based on status and location filter
  const mapMalls = malls.filter((m) => {
    // Must have coordinates
    if (!m.coordinates?.lat || !m.coordinates?.lng) return false;
    
    // Status filter - 'all' shows both existing and upcoming
    const matchesStatus = statusFilter === 'all' || m.category === statusFilter;
    
    // Location filters
    const matchesState = mapStateFilter === 'all' || m.state === mapStateFilter;
    const matchesCity = mapCityFilter === 'all' || m.city === mapCityFilter;
    const matchesArea = mapAreaFilter === 'all' || m.area === mapAreaFilter;
    
    return matchesStatus && matchesState && matchesCity && matchesArea;
  });
  
  // Clear map filters
  const clearMapFilters = () => {
    setMapStateFilter('all');
    setMapCityFilter('all');
    setMapAreaFilter('all');
  };
  
  // Check if any map filter is active
  const hasActiveMapFilters = mapStateFilter !== 'all' || mapCityFilter !== 'all' || mapAreaFilter !== 'all';
  
  // Search suggestions - filter malls by name, state, city, or area match
  const searchSuggestions = searchQuery.trim().length >= 2
    ? malls
        .filter((mall) => {
          const query = searchQuery.toLowerCase();
          const matchesName = mall.name.toLowerCase().includes(query);
          const matchesState = mall.state?.toLowerCase().includes(query);
          const matchesCity = mall.city?.toLowerCase().includes(query);
          const matchesArea = mall.area?.toLowerCase().includes(query);
          const matchesAddress = mall.address?.toLowerCase().includes(query);
          return matchesName || matchesState || matchesCity || matchesArea || matchesAddress;
        })
        .slice(0, 8) // Limit to 8 suggestions
    : [];
  
  // Handle click outside to close suggestions
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (searchContainerRef.current && !searchContainerRef.current.contains(event.target as Node)) {
        setShowSuggestions(false);
      }
      if (searchContainerRef2.current && !searchContainerRef2.current.contains(event.target as Node)) {
        setShowSuggestions2(false);
      }
    };
    
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);
  
  // Handle suggestion click - navigate directly to mall page
  const handleSuggestionClick = (mallId: string) => {
    setShowSuggestions(false);
    setShowSuggestions2(false);
    setSearchQuery('');
    router.push(`/mall/${mallId}`);
  };
  
  // Handle search input focus
  const handleSearchFocus = () => {
    if (searchQuery.trim().length >= 2 && searchSuggestions.length > 0) {
      setShowSuggestions(true);
    }
  };
  
  // Handle search input change
  const handleSearchChange = (value: string) => {
    setSearchQuery(value);
    if (value.trim().length >= 2) {
      setShowSuggestions(true);
    } else {
      setShowSuggestions(false);
    }
  };
  
  // Get malls to display based on filter
  const displayMalls = statusFilter === 'upcoming' 
    ? filteredMalls.filter((m) => m.category === 'upcoming')
    : statusFilter === 'existing'
      ? filteredMalls.filter((m) => m.category === 'existing')
      : filteredMalls;

  // Pagination
  const totalPages = Math.ceil(displayMalls.length / MALLS_PER_PAGE);
  const paginatedMalls = displayMalls.slice(
    (currentPage - 1) * MALLS_PER_PAGE,
    currentPage * MALLS_PER_PAGE
  );

  // Reset page when filters change
  useEffect(() => {
    setCurrentPage(1);
  }, [searchQuery, statusFilter]);

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900">
      {/* Header Advertisement */}
      <AdvertisementBanner position="HEADER" />
      
      {/* Header */}
      <header className="bg-white dark:bg-slate-900 border-b shadow-sm sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <Link href="/" className="flex items-center gap-2 sm:gap-3">
              {siteSettings.siteLogo ? (
                <div className="relative w-10 h-10 sm:w-12 sm:h-12">
                  <Image
                    src={siteSettings.siteLogo}
                    alt={siteSettings.siteName}
                    fill
                    className="object-contain"
                    sizes="(max-width: 640px) 40px, 48px"
                  />
                </div>
              ) : (
                <div className="bg-gradient-to-r from-emerald-500 to-teal-500 p-2 rounded-lg">
                  <Building2 className="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
              )}
              <div>
                <h1 className="text-lg sm:text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                  {siteSettings.siteName}
                </h1>
                <p className="text-xs text-muted-foreground hidden sm:block">
                  {siteSettings.siteTagline}
                </p>
              </div>
            </Link>

            {/* Desktop Navigation Links */}
            <nav className="hidden sm:flex items-center gap-1 sm:gap-2">
              <Link href="/">
                <Button variant="ghost" size="sm">
                  Home
                </Button>
              </Link>
              <Link href="/about">
                <Button variant="ghost" size="sm">
                  About Us
                </Button>
              </Link>
              <Link href="/contact">
                <Button variant="ghost" size="sm">
                  Contact Us
                </Button>
              </Link>
              
              {/* Social Links in Header */}
              {(siteSettings.facebook || siteSettings.twitter || siteSettings.instagram || siteSettings.linkedin || siteSettings.youtube || siteSettings.whatsapp) && (
                <div className="hidden lg:flex items-center ml-2 pl-2 border-l">
                  <SocialLinks links={siteSettings} iconSize="sm" />
                </div>
              )}
            </nav>

            {/* Mobile Menu Button */}
            <Button
              variant="ghost"
              size="icon"
              className="sm:hidden"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </Button>
          </div>

          {/* Mobile Navigation Menu */}
          {mobileMenuOpen && (
            <nav className="sm:hidden pb-4 border-t pt-2">
              {/* Social Links in Mobile Menu */}
              {(siteSettings.facebook || siteSettings.twitter || siteSettings.instagram || siteSettings.linkedin || siteSettings.youtube || siteSettings.whatsapp) && (
                <div className="mb-3 pb-3 border-b px-4">
                  <p className="text-xs text-muted-foreground mb-2">Follow us:</p>
                  <SocialLinks links={siteSettings} iconSize="sm" />
                </div>
              )}
              <div className="flex flex-col gap-1">
                <Link href="/" onClick={() => setMobileMenuOpen(false)}>
                  <Button variant="ghost" className="w-full justify-start">
                    Home
                  </Button>
                </Link>
                <Link href="/about" onClick={() => setMobileMenuOpen(false)}>
                  <Button variant="ghost" className="w-full justify-start">
                    About Us
                  </Button>
                </Link>
                <Link href="/contact" onClick={() => setMobileMenuOpen(false)}>
                  <Button variant="ghost" className="w-full justify-start">
                    Contact Us
                  </Button>
                </Link>
              </div>
            </nav>
          )}
        </div>
      </header>

      {/* Hero Section - Compact */}
      <section className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-6 sm:py-10">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-2xl sm:text-3xl font-bold mb-2">
            Find Your Perfect Shopping Destination
          </h2>
          <p className="text-sm sm:text-base text-emerald-100 max-w-2xl mx-auto mb-4">
            Explore the best shopping malls in your area.
          </p>

          {/* Quick Stats */}
          <div className="flex flex-wrap justify-center gap-4 sm:gap-8">
            <div className="text-center">
              <div className="text-xl sm:text-2xl font-bold">{totalMalls}</div>
              <div className="text-emerald-200 text-xs">Shopping Malls</div>
            </div>
            <div className="text-center">
              <div className="text-xl sm:text-2xl font-bold">{existingMalls}</div>
              <div className="text-emerald-200 text-xs">Existing Malls</div>
            </div>
            <div className="text-center">
              <div className="text-xl sm:text-2xl font-bold">{upcomingMalls}</div>
              <div className="text-emerald-200 text-xs">Upcoming Malls</div>
            </div>
          </div>
        </div>
      </section>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="flex gap-6">
          {/* Left Sidebar Ads - Hidden on small screens */}
          <aside className="hidden xl:block w-48 shrink-0">
            <div className="sticky top-24">
              <SidebarAdvertisements position="LEFT_SIDEBAR" />
            </div>
          </aside>

          {/* Main Content */}
          <div className="flex-1 min-w-0">
        {/* Social Share Buttons */}
        <div className="mb-4 flex items-center justify-between">
          <SocialShareButtons 
            title="MallFinder - Find Your Perfect Shopping Destination"
            description="Explore the best shopping malls in your area"
            variant="icons"
            size="sm"
          />
        </div>

        {/* Search and Filter */}
        <Card className="mb-6">
          <CardContent className="p-3 sm:p-4">
            <div className="flex flex-col sm:flex-row gap-2 sm:gap-3">
              <div className="relative flex-1" ref={searchContainerRef}>
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground z-10" />
                <Input
                  placeholder="Search malls by name, state, city, area, or address..."
                  value={searchQuery}
                  onChange={(e) => handleSearchChange(e.target.value)}
                  onFocus={handleSearchFocus}
                  onKeyDown={(e) => {
                    if (e.key === 'Enter') {
                      setCurrentPage(1);
                      setShowSuggestions(false);
                    }
                    if (e.key === 'Escape') {
                      setShowSuggestions(false);
                    }
                  }}
                  className="pl-10 h-9"
                  autoComplete="off"
                />
                
                {/* Search Suggestions Dropdown */}
                {showSuggestions && searchSuggestions.length > 0 && (
                  <div className="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                    <div className="p-2 text-xs text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700">
                      Suggestions ({searchSuggestions.length})
                    </div>
                    {searchSuggestions.map((mall) => (
                      <button
                        key={mall.id}
                        type="button"
                        className="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-3 transition-colors"
                        onClick={() => handleSuggestionClick(mall.id)}
                      >
                        <div className="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-md flex-shrink-0 overflow-hidden">
                          {mall.imageUrl ? (
                            <Image
                              src={mall.imageUrl}
                              alt={mall.name}
                              width={40}
                              height={40}
                              className="object-cover w-full h-full"
                            />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center">
                              <Building2 className="w-5 h-5 text-slate-400" />
                            </div>
                          )}
                        </div>
                        <div className="flex-1 min-w-0">
                          <div className="font-medium text-slate-900 dark:text-white truncate">
                            {mall.name}
                          </div>
                          <div className="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {[mall.area, mall.city, mall.state].filter(Boolean).join(', ')}
                          </div>
                        </div>
                        <Badge 
                          variant={mall.category === 'existing' ? 'default' : 'secondary'}
                          className={`text-xs flex-shrink-0 ${
                            mall.category === 'existing' 
                              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' 
                              : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                          }`}
                        >
                          {mall.category === 'existing' ? 'Existing' : 'Upcoming'}
                        </Badge>
                      </button>
                    ))}
                  </div>
                )}
              </div>

              <Button
                onClick={() => {
                  setCurrentPage(1);
                  setShowSuggestions(false);
                  // Scroll to results section
                  const resultsSection = document.getElementById('mall-results');
                  if (resultsSection) {
                    resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }
                }}
                className={`h-9 text-white gap-2 ${
                  statusFilter === 'upcoming'
                    ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600'
                    : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
                }`}
              >
                <Search className="w-4 h-4" />
                <span className="hidden sm:inline">Search</span>
              </Button>

              <Select value={statusFilter} onValueChange={setStatusFilter}>
                <SelectTrigger className="w-full sm:w-40 h-9">
                  <SelectValue placeholder="Filter by status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Malls</SelectItem>
                  <SelectItem value="existing">Existing Malls</SelectItem>
                  <SelectItem value="upcoming">Upcoming Malls</SelectItem>
                </SelectContent>
              </Select>

              <Button
                onClick={() => setShowMap(!showMap)}
                className={`hidden sm:flex h-9 gap-2 text-white ${
                  statusFilter === 'upcoming'
                    ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600'
                    : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
                }`}
              >
                <MapPin className="w-4 h-4" />
                {showMap ? 'Hide Map' : 'Show Map'}
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Map Section Header & Filters */}
        {showMap && (
          <div className="mb-4">
            <div className="flex items-center justify-between flex-wrap gap-2 mb-3">
              <div className="flex items-center gap-3">
                <div className="bg-gradient-to-r from-emerald-500 to-teal-500 p-2 rounded-lg">
                  <MapPin className="w-5 h-5 text-white" />
                </div>
                <div>
                  <h3 className="font-semibold text-slate-900 dark:text-white">
                    Explore Malls on Map
                  </h3>
                  <p className="text-sm text-slate-500 dark:text-slate-400">
                    Filter by location and click on markers to view mall details
                  </p>
                </div>
              </div>
              <div className="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span className="flex items-center gap-1">
                  <span className="w-3 h-3 rounded-full bg-emerald-500"></span>
                  Existing
                </span>
                <span className="flex items-center gap-1">
                  <span className="w-3 h-3 rounded-full bg-amber-500"></span>
                  Upcoming
                </span>
              </div>
            </div>
            
            {/* Map Location Filters */}
            <div className="flex flex-wrap items-center gap-2 p-3 bg-slate-50 dark:bg-slate-800/50 rounded-lg border">
              <div className="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                <Filter className="w-4 h-4" />
                <span>Filter by:</span>
              </div>
              
              {/* State Filter */}
              <Select value={mapStateFilter} onValueChange={setMapStateFilter}>
                <SelectTrigger className="w-[140px] h-8 text-sm">
                  <SelectValue placeholder="All States" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All States</SelectItem>
                  {states.map((state) => (
                    <SelectItem key={state.id} value={state.name}>
                      {state.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              {/* City Filter */}
              <Select 
                value={mapCityFilter} 
                onValueChange={setMapCityFilter}
                disabled={mapStateFilter === 'all'}
              >
                <SelectTrigger className="w-[140px] h-8 text-sm">
                  <SelectValue placeholder="All Cities" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Cities</SelectItem>
                  {citiesForMapState.map((city) => (
                    <SelectItem key={city.id} value={city.name}>
                      {city.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              {/* Area Filter */}
              <Select 
                value={mapAreaFilter} 
                onValueChange={setMapAreaFilter}
                disabled={mapCityFilter === 'all'}
              >
                <SelectTrigger className="w-[140px] h-8 text-sm">
                  <SelectValue placeholder="All Areas" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Areas</SelectItem>
                  {areasForMapCity.map((area) => (
                    <SelectItem key={area.id} value={area.name}>
                      {area.name}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              {/* Clear Filters Button */}
              {hasActiveMapFilters && (
                <Button
                  variant="ghost"
                  size="sm"
                  className="h-8 text-sm text-slate-600 dark:text-slate-400"
                  onClick={clearMapFilters}
                >
                  <RefreshCw className="w-3 h-3 mr-1" />
                  Clear
                </Button>
              )}
              
              {/* Mall count indicator */}
              <div className="ml-auto text-xs text-slate-500 dark:text-slate-400">
                Showing {mapMalls.length} mall{mapMalls.length !== 1 ? 's' : ''} on map
              </div>
            </div>
          </div>
        )}

        {/* Map - Horizontal Full Width */}
        {showMap && (
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            className="h-[300px] sm:h-[400px] rounded-lg overflow-hidden shadow-lg border bg-white mb-6"
          >
            <MallMap
              malls={mapMalls}
              selectedMall={selectedMall}
              onMallSelect={setSelectedMall}
            />
          </motion.div>
        )}

        {/* Search Section Below Map */}
        <Card className="mb-6">
          <CardContent className="p-3 sm:p-4">
            <div className="flex flex-col sm:flex-row gap-2 sm:gap-3">
              <div className="relative flex-1" ref={searchContainerRef2}>
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground z-10" />
                <Input
                  placeholder="Search malls by name, state, city, area, or address..."
                  value={searchQuery}
                  onChange={(e) => {
                    handleSearchChange(e.target.value);
                    if (e.target.value.trim().length >= 2) {
                      setShowSuggestions2(true);
                    }
                  }}
                  onFocus={() => {
                    if (searchQuery.trim().length >= 2 && searchSuggestions.length > 0) {
                      setShowSuggestions2(true);
                    }
                  }}
                  onKeyDown={(e) => {
                    if (e.key === 'Enter') {
                      setCurrentPage(1);
                      setShowSuggestions2(false);
                    }
                    if (e.key === 'Escape') {
                      setShowSuggestions2(false);
                    }
                  }}
                  className="pl-10 h-9"
                  autoComplete="off"
                />
                
                {/* Search Suggestions Dropdown */}
                {showSuggestions2 && searchSuggestions.length > 0 && (
                  <div className="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                    <div className="p-2 text-xs text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700">
                      Suggestions ({searchSuggestions.length})
                    </div>
                    {searchSuggestions.map((mall) => (
                      <button
                        key={mall.id}
                        type="button"
                        className="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-3 transition-colors"
                        onClick={() => handleSuggestionClick(mall.id)}
                      >
                        <div className="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-md flex-shrink-0 overflow-hidden">
                          {mall.imageUrl ? (
                            <Image
                              src={mall.imageUrl}
                              alt={mall.name}
                              width={40}
                              height={40}
                              className="object-cover w-full h-full"
                            />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center">
                              <Building2 className="w-5 h-5 text-slate-400" />
                            </div>
                          )}
                        </div>
                        <div className="flex-1 min-w-0">
                          <div className="font-medium text-slate-900 dark:text-white truncate">
                            {mall.name}
                          </div>
                          <div className="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {[mall.area, mall.city, mall.state].filter(Boolean).join(', ')}
                          </div>
                        </div>
                        <Badge 
                          variant={mall.category === 'existing' ? 'default' : 'secondary'}
                          className={`text-xs flex-shrink-0 ${
                            mall.category === 'existing' 
                              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' 
                              : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                          }`}
                        >
                          {mall.category === 'existing' ? 'Existing' : 'Upcoming'}
                        </Badge>
                      </button>
                    ))}
                  </div>
                )}
              </div>

              <Button
                onClick={() => {
                  setCurrentPage(1);
                  setShowSuggestions2(false);
                  const resultsSection = document.getElementById('mall-results');
                  if (resultsSection) {
                    resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }
                }}
                className={`h-9 text-white gap-2 ${
                  statusFilter === 'upcoming'
                    ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600'
                    : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
                }`}
              >
                <Search className="w-4 h-4" />
                <span className="hidden sm:inline">Search</span>
              </Button>

              <Select value={statusFilter} onValueChange={setStatusFilter}>
                <SelectTrigger className="w-full sm:w-40 h-9">
                  <SelectValue placeholder="Filter by status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Malls</SelectItem>
                  <SelectItem value="existing">Existing Malls</SelectItem>
                  <SelectItem value="upcoming">Upcoming Malls</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </CardContent>
        </Card>

        {/* Section Divider - Browse Malls */}
        <div className="flex items-center gap-4 mb-6">
          <div className="flex-1 h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent dark:via-slate-700"></div>
          <div className="flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 rounded-full shadow-sm border">
            <Building2 className={`w-4 h-4 ${statusFilter === 'upcoming' ? 'text-amber-500' : 'text-emerald-500'}`} />
            <span className="text-sm font-medium text-slate-600 dark:text-slate-300">Browse Shopping Malls</span>
          </div>
          <div className="flex-1 h-px bg-gradient-to-r from-transparent via-slate-300 to-transparent dark:via-slate-700"></div>
        </div>

        {/* Mall Type Toggle Buttons - Below Map */}
        <div className="grid grid-cols-2 gap-3 sm:gap-4 mb-6">
          <Button
            onClick={() => setStatusFilter('existing')}
            className={`h-auto py-3 sm:py-4 flex-col sm:flex-row gap-1 sm:gap-2 ${
              statusFilter === 'existing' || statusFilter === 'all'
                ? 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white shadow-lg'
                : 'bg-white dark:bg-slate-800 border-2 border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-700'
            }`}
          >
            <Store className="w-5 h-5" />
            <div className="flex flex-col sm:flex-row items-center gap-0 sm:gap-2">
              <span className="font-semibold">Existing Malls</span>
              <span className="text-sm opacity-80">({existingMalls})</span>
            </div>
          </Button>
          
          <Button
            onClick={() => setStatusFilter(statusFilter === 'upcoming' ? 'all' : 'upcoming')}
            className={`h-auto py-3 sm:py-4 flex-col sm:flex-row gap-1 sm:gap-2 ${
              statusFilter === 'upcoming'
                ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white shadow-lg'
                : 'bg-white dark:bg-slate-800 border-2 border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-slate-700'
            }`}
          >
            <Sparkles className="w-5 h-5" />
            <div className="flex flex-col sm:flex-row items-center gap-0 sm:gap-2">
              <span className="font-semibold">Upcoming Malls</span>
              <span className="text-sm opacity-80">({upcomingMalls})</span>
            </div>
          </Button>
        </div>

        {/* Search Results Info */}
        {searchQuery.trim() && (
          <div id="mall-results" className="mb-4 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2 text-emerald-700 dark:text-emerald-400">
                <Search className="w-4 h-4" />
                <span className="font-medium">
                  {displayMalls.length} result{displayMalls.length !== 1 ? 's' : ''} for &quot;{searchQuery}&quot;
                </span>
              </div>
              <Button
                variant="ghost"
                size="sm"
                onClick={() => setSearchQuery('')}
                className="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700"
              >
                <X className="w-4 h-4 mr-1" />
                Clear
              </Button>
            </div>
          </div>
        )}

        {/* Malls Display */}
        <div id="mall-results">
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
            </div>
          ) : displayMalls.length === 0 ? (
            <div className="text-center py-12">
              {statusFilter === 'upcoming' ? (
                <>
                  <Sparkles className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                  <h3 className="text-lg font-semibold mb-2">No upcoming malls</h3>
                  <p className="text-muted-foreground">
                    Check back soon for new shopping destinations
                  </p>
                </>
              ) : (
                <>
                  <Store className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                  <h3 className="text-lg font-semibold mb-2">No malls found</h3>
                  <p className="text-muted-foreground">
                    Try adjusting your search or filter criteria
                  </p>
                </>
              )}
            </div>
          ) : (
            <>
              <div className="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                <AnimatePresence mode="popLayout">
                  {paginatedMalls.map((mall) => (
                    <MallCard
                      key={mall.id}
                      mall={mall}
                      isSelected={selectedMall?.id === mall.id}
                      onClick={() => router.push(`/mall/${mall.id}`)}
                      showViewButton
                    />
                  ))}
                </AnimatePresence>
              </div>

              {/* Pagination Controls */}
              {totalPages > 1 && (
                <div className="flex items-center justify-center gap-2 mt-8 pt-6 border-t">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => {
                      setCurrentPage(prev => Math.max(1, prev - 1));
                      document.getElementById('mall-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }}
                    disabled={currentPage === 1}
                    className="gap-1"
                  >
                    <ChevronLeft className="w-4 h-4" />
                    Previous
                  </Button>

                  <div className="flex items-center gap-1">
                    {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                      let pageNum;
                      if (totalPages <= 5) {
                        pageNum = i + 1;
                      } else if (currentPage <= 3) {
                        pageNum = i + 1;
                      } else if (currentPage >= totalPages - 2) {
                        pageNum = totalPages - 4 + i;
                      } else {
                        pageNum = currentPage - 2 + i;
                      }

                      return (
                        <Button
                          key={pageNum}
                          variant={currentPage === pageNum ? "default" : "outline"}
                          size="sm"
                          className={`w-8 h-8 p-0 ${currentPage === pageNum ? 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600' : ''}`}
                          onClick={() => {
                            setCurrentPage(pageNum);
                            document.getElementById('mall-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                          }}
                        >
                          {pageNum}
                        </Button>
                      );
                    })}
                  </div>

                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => {
                      setCurrentPage(prev => Math.min(totalPages, prev + 1));
                      document.getElementById('mall-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }}
                    disabled={currentPage === totalPages}
                    className="gap-1"
                  >
                    Next
                    <ChevronRight className="w-4 h-4" />
                  </Button>
                </div>
              )}

              {/* Page indicator */}
              {totalPages > 1 && (
                <p className="text-center text-sm text-muted-foreground mt-2">
                  Showing {((currentPage - 1) * MALLS_PER_PAGE) + 1} - {Math.min(currentPage * MALLS_PER_PAGE, displayMalls.length)} of {displayMalls.length} malls
                </p>
              )}
            </>
          )}
        </div>
          </div>

          {/* Right Sidebar Ads - Hidden on small screens */}
          <aside className="hidden xl:block w-48 shrink-0">
            <div className="sticky top-24">
              <SidebarAdvertisements position="RIGHT_SIDEBAR" />
            </div>
          </aside>
        </div>
      </main>

      {/* Explore Other Malls Section */}
      <section className="bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 dark:from-slate-800 dark:via-slate-800 dark:to-slate-900 py-10 sm:py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Section Header */}
          <div className="text-center mb-8">
            <h2 className="text-2xl sm:text-3xl font-bold mb-2">
              <span className="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                Explore Other Malls
              </span>
            </h2>
            <p className="text-muted-foreground text-sm sm:text-base">
              Discover more shopping destinations near you
            </p>
          </div>

          {/* Mall Carousel */}
          {malls.length > 0 ? (
            <div className="relative">
              {/* Navigation Buttons */}
              <Button
                variant="outline"
                size="icon"
                className="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 dark:bg-slate-800/90 shadow-lg rounded-full h-10 w-10 -ml-4 sm:ml-0"
                onClick={() => {
                  if (exploreCarouselRef.current) {
                    exploreCarouselRef.current.scrollBy({ left: -300, behavior: 'smooth' });
                  }
                }}
              >
                <ChevronLeft className="w-5 h-5" />
              </Button>

              {/* Scrollable Container */}
              <div
                ref={exploreCarouselRef}
                className="flex gap-4 overflow-x-auto pb-4 scroll-smooth"
                style={{ scrollbarWidth: 'none', msOverflowStyle: 'none' }}
              >
                {malls.slice(0, 10).map((mall) => (
                  <Link
                    key={mall.id}
                    href={`/mall/${mall.id}`}
                    className="flex-shrink-0 w-64 sm:w-72 group"
                  >
                    <Card className={`overflow-hidden h-full transition-all duration-300 hover:shadow-xl hover:scale-[1.02] ${
                      mall.category === 'upcoming'
                        ? 'border-2 border-amber-300 dark:border-amber-700'
                        : 'border-2 border-emerald-300 dark:border-emerald-700'
                    }`}>
                      {/* Image */}
                      <div className="relative h-36 sm:h-40 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800">
                        {mall.imageUrl ? (
                          <Image
                            src={mall.imageUrl}
                            alt={mall.name}
                            fill
                            className="object-cover group-hover:scale-105 transition-transform duration-300"
                            sizes="288px"
                          />
                        ) : (
                          <div className="absolute inset-0 flex items-center justify-center">
                            <Building2 className="w-12 h-12 text-slate-400 dark:text-slate-500" />
                          </div>
                        )}
                        {/* Category Badge */}
                        <div className="absolute top-2 left-2">
                          <span className={`text-xs font-medium px-2 py-1 rounded-full ${
                            mall.category === 'upcoming'
                              ? 'bg-amber-500 text-white'
                              : 'bg-emerald-500 text-white'
                          }`}>
                            {mall.category === 'upcoming' ? 'Upcoming' : 'Existing'}
                          </span>
                        </div>
                      </div>

                      {/* Content */}
                      <CardContent className="p-3">
                        <h3 className="font-semibold text-sm sm:text-base truncate mb-1">
                          {mall.name}
                        </h3>
                        <p className="text-xs text-muted-foreground truncate mb-2">
                          {mall.area || mall.address}
                        </p>
                        {mall.description && (
                          <p className="text-xs text-muted-foreground line-clamp-2">
                            {mall.description}
                          </p>
                        )}
                      </CardContent>
                    </Card>
                  </Link>
                ))}
              </div>

              <Button
                variant="outline"
                size="icon"
                className="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-white/90 dark:bg-slate-800/90 shadow-lg rounded-full h-10 w-10 -mr-4 sm:mr-0"
                onClick={() => {
                  if (exploreCarouselRef.current) {
                    exploreCarouselRef.current.scrollBy({ left: 300, behavior: 'smooth' });
                  }
                }}
              >
                <ChevronRight className="w-5 h-5" />
              </Button>
            </div>
          ) : (
            <div className="text-center py-8">
              <Building2 className="w-12 h-12 text-muted-foreground mx-auto mb-3" />
              <p className="text-muted-foreground">No malls to explore</p>
            </div>
          )}

          {/* View All Button */}
          {malls.length > 0 && (
            <div className="text-center mt-6">
              <Button
                onClick={() => {
                  setStatusFilter('all');
                  window.scrollTo({ top: 0, behavior: 'smooth' });
                }}
                className="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white"
              >
                View All Malls
              </Button>
            </div>
          )}
        </div>
      </section>

      {/* Footer Advertisement */}
      <AdvertisementBanner position="FOOTER" />

      {/* Footer */}
      <SiteFooter />
    </div>
  );
}

'use client';

import { useState, useEffect, use, useRef } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import dynamic from 'next/dynamic';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { StoreCard } from '@/components/store/StoreCard';
import { StoreForm } from '@/components/store/StoreForm';
import { MallJsonLd } from '@/components/seo/JsonLd';
import { AdvertisementBanner, SidebarAdvertisements } from '@/components/advertisement/AdvertisementBanner';
import { SocialShareButtons } from '@/components/ui/social-share-buttons';
import { SocialLinks } from '@/components/ui/social-links';
import {
  ArrowLeft,
  MapPin,
  Clock,
  Calendar,
  Building2,
  Store as StoreIcon,
  Plus,
  Search,
  Filter,
  Loader2,
  Sparkles,
  Phone,
  Globe,
  Users,
  Trash2,
  Pencil,
  LogOut,
  Camera,
  ImagePlus,
  X,
  ChevronLeft,
  ChevronRight,
  Menu,
} from 'lucide-react';
import { toast } from 'sonner';
import { motion, AnimatePresence } from 'framer-motion';
import type { Mall } from '@/types/mall';
import type { Store } from '@/types/store';
import { DEFAULT_STORE_CATEGORIES } from '@/types/store';
import type { StoreCategory } from '@/types/category';

// Dynamic import for map
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

interface PageProps {
  params: Promise<{ id: string }>;
}

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
  pinterest?: string;
}

export default function MallLandingPage({ params }: PageProps) {
  const { id } = use(params);
  const router = useRouter();
  const [mall, setMall] = useState<Mall | null>(null);
  const [stores, setStores] = useState<Store[]>([]);
  const [categories, setCategories] = useState<StoreCategory[]>([]);
  const [suggestedMalls, setSuggestedMalls] = useState<Mall[]>([]);
  const [loading, setLoading] = useState(true);
  const [storesLoading, setStoresLoading] = useState(true);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
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
    pinterest: '',
  });
  
  // Authentication state
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [authChecking, setAuthChecking] = useState(true);
  
  // Filter states
  const [searchQuery, setSearchQuery] = useState('');
  const [categoryFilter, setCategoryFilter] = useState<string>('all');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  
  // Form states
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [editingStore, setEditingStore] = useState<Store | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  // Delete confirmation
  const [storeToDelete, setStoreToDelete] = useState<Store | null>(null);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  
  // Mall image management states
  const [isUploadingImage, setIsUploadingImage] = useState(false);
  const [isDeletingImage, setIsDeletingImage] = useState(false);
  const [isDeleteMallImageDialogOpen, setIsDeleteMallImageDialogOpen] = useState(false);
  
  // Store pagination state
  const [currentStorePage, setCurrentStorePage] = useState(1);
  const STORES_PER_PAGE = 10;
  const exploreCarouselRef = useRef<HTMLDivElement>(null);
  
  // Mall search states (for searching other malls)
  const [mallSearchQuery, setMallSearchQuery] = useState('');
  const [showMallSuggestions, setShowMallSuggestions] = useState(false);
  const [allMalls, setAllMalls] = useState<Mall[]>([]);
  const mallSearchContainerRef = useRef<HTMLDivElement>(null);
  
  // Fetch all malls for search suggestions
  useEffect(() => {
    const fetchAllMalls = async () => {
      try {
        const response = await fetch('/api/malls');
        if (response.ok) {
          const data = await response.json();
          setAllMalls(data);
        }
      } catch {
        // Ignore errors
      }
    };
    fetchAllMalls();
  }, []);
  
  // Mall search suggestions
  const mallSearchSuggestions = mallSearchQuery.trim().length >= 2
    ? allMalls
        .filter((m) => {
          const query = mallSearchQuery.toLowerCase();
          const matchesName = m.name.toLowerCase().includes(query);
          const matchesState = m.state?.toLowerCase().includes(query);
          const matchesCity = m.city?.toLowerCase().includes(query);
          const matchesArea = m.area?.toLowerCase().includes(query);
          const matchesAddress = m.address?.toLowerCase().includes(query);
          return matchesName || matchesState || matchesCity || matchesArea || matchesAddress;
        })
        .slice(0, 8)
    : [];
  
  // Handle click outside to close mall suggestions
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (mallSearchContainerRef.current && !mallSearchContainerRef.current.contains(event.target as Node)) {
        setShowMallSuggestions(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);
  
  // Handle mall suggestion click
  const handleMallSuggestionClick = (mallId: string) => {
    setShowMallSuggestions(false);
    setMallSearchQuery('');
    router.push(`/mall/${mallId}`);
  };
  
  // Fetch site settings
  useEffect(() => {
    const fetchSettings = async () => {
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
            pinterest: data.pinterest || '',
          });
        }
      } catch {
        // Use defaults
      }
    };
    fetchSettings();
  }, []);
  
  // Check authentication
  useEffect(() => {
    const checkAuth = async () => {
      try {
        const response = await fetch('/api/auth/session');
        const data = await response.json();
        setIsAuthenticated(data.authenticated || false);
      } catch {
        setIsAuthenticated(false);
      } finally {
        setAuthChecking(false);
      }
    };
    checkAuth();
  }, []);

  // Fetch mall data
  useEffect(() => {
    const fetchMall = async () => {
      try {
        setLoading(true);
        const response = await fetch(`/api/malls/${id}`);
        if (!response.ok) throw new Error('Mall not found');
        const data = await response.json();
        setMall(data);
      } catch {
        toast.error('Failed to load mall');
      } finally {
        setLoading(false);
      }
    };

    fetchMall();
  }, [id]);

  // Fetch suggested malls (other malls excluding current)
  useEffect(() => {
    const fetchSuggestedMalls = async () => {
      try {
        const response = await fetch('/api/malls');
        if (response.ok) {
          const data = await response.json();
          // Filter out current mall and limit to 10 suggestions
          const otherMalls = data.filter((m: Mall) => m.id !== id).slice(0, 10);
          setSuggestedMalls(otherMalls);
        }
      } catch {
        // Ignore errors for suggested malls
      }
    };

    if (id) {
      fetchSuggestedMalls();
    }
  }, [id]);

  // Fetch categories
  const fetchCategories = async () => {
    try {
      const response = await fetch('/api/categories');
      if (response.ok) {
        const data = await response.json();
        setCategories(data);
      }
    } catch {
      // Ignore errors for categories
    }
  };

  useEffect(() => {
    fetchCategories();
  }, []);

  // Use database categories if available, otherwise use defaults
  const categoryOptions = categories.length > 0 
    ? categories.map(c => c.name) 
    : DEFAULT_STORE_CATEGORIES;

  // Fetch stores
  useEffect(() => {
    const fetchStores = async () => {
      try {
        setStoresLoading(true);
        const params = new URLSearchParams();
        if (categoryFilter !== 'all') params.append('category', categoryFilter);
        if (statusFilter !== 'all') params.append('status', statusFilter);
        
        const response = await fetch(`/api/malls/${id}/stores?${params.toString()}`);
        if (!response.ok) throw new Error('Failed to fetch stores');
        const data = await response.json();
        setStores(data);
      } catch {
        toast.error('Failed to load stores');
      } finally {
        setStoresLoading(false);
      }
    };

    fetchStores();
  }, [id, categoryFilter, statusFilter]);

  // Filter stores by search
  const filteredStores = stores.filter((store) => {
    if (!searchQuery) return true;
    const query = searchQuery.toLowerCase();
    return (
      store.name.toLowerCase().includes(query) ||
      store.description?.toLowerCase().includes(query) ||
      store.category.toLowerCase().includes(query) ||
      store.floor?.toLowerCase().includes(query)
    );
  });

  // Group stores by category
  const storesByCategory = filteredStores.reduce((acc, store) => {
    if (!acc[store.category]) {
      acc[store.category] = [];
    }
    acc[store.category].push(store);
    return acc;
  }, {} as Record<string, Store[]>);

  // Statistics
  const openStores = stores.filter((s) => s.status === 'OPEN').length;
  const comingSoonStores = stores.filter((s) => s.status === 'COMING_SOON').length;
  const closedStores = stores.filter((s) => s.status === 'CLOSED').length;
  
  // Store pagination
  const totalFilteredPages = Math.ceil(filteredStores.length / STORES_PER_PAGE);
  const paginatedStores = filteredStores.slice(
    (currentStorePage - 1) * STORES_PER_PAGE,
    currentStorePage * STORES_PER_PAGE
  );
  
  // Reset page when filters change
  useEffect(() => {
    setCurrentStorePage(1);
  }, [searchQuery, categoryFilter, statusFilter]);

  // Handle form submission
  const handleFormSubmit = async (data: Omit<Store, 'id' | 'mallId' | 'createdAt' | 'updatedAt' | 'images'>, images: { url: string; sortOrder: number }[]) => {
    setIsSubmitting(true);
    try {
      const url = editingStore ? `/api/stores/${editingStore.id}` : '/api/stores';
      const method = editingStore ? 'PUT' : 'POST';
      
      const response = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          ...data,
          mallId: id,
          images,
        }),
      });

      if (!response.ok) throw new Error('Failed to save store');

      toast.success(editingStore ? 'Store updated successfully' : 'Store added successfully');
      setIsFormOpen(false);
      setEditingStore(null);
      
      // Refresh stores
      const storesResponse = await fetch(`/api/malls/${id}/stores`);
      if (storesResponse.ok) {
        const storesData = await storesResponse.json();
        setStores(storesData);
      }
    } catch {
      toast.error('Failed to save store');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Handle delete
  const handleDeleteConfirm = async () => {
    if (!storeToDelete) return;
    
    try {
      const response = await fetch(`/api/stores/${storeToDelete.id}`, {
        method: 'DELETE',
      });
      
      if (!response.ok) throw new Error('Failed to delete store');
      
      toast.success('Store deleted successfully');
      setStores(stores.filter((s) => s.id !== storeToDelete.id));
    } catch {
      toast.error('Failed to delete store');
    } finally {
      setIsDeleteDialogOpen(false);
      setStoreToDelete(null);
    }
  };

  // Handle mall image upload
  const handleMallImageUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file || !mall) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
      toast.error('Please select an image file');
      return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
      toast.error('Image size should be less than 5MB');
      return;
    }

    setIsUploadingImage(true);
    try {
      // Convert to base64
      const reader = new FileReader();
      reader.onload = async () => {
        try {
          const base64 = reader.result as string;

          // Upload image
          const uploadResponse = await fetch('/api/upload', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              image: base64,
              type: 'malls',
            }),
          });

          if (!uploadResponse.ok) throw new Error('Failed to upload image');
          const { url } = await uploadResponse.json();

          // Update mall with new image URL
          const updateResponse = await fetch(`/api/malls/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ imageUrl: url }),
          });

          if (!updateResponse.ok) throw new Error('Failed to update mall');

          // Delete old image if exists
          if (mall.imageUrl && mall.imageUrl.startsWith('/malls/')) {
            await fetch('/api/upload', {
              method: 'DELETE',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ url: mall.imageUrl }),
            });
          }

          toast.success('Mall image uploaded successfully');
          setMall({ ...mall, imageUrl: url });
        } catch {
          toast.error('Failed to upload image');
        } finally {
          setIsUploadingImage(false);
        }
      };
      reader.readAsDataURL(file);
    } catch {
      toast.error('Failed to read image file');
      setIsUploadingImage(false);
    }

    // Reset input
    e.target.value = '';
  };

  // Handle mall image delete
  const handleMallImageDelete = async () => {
    if (!mall || !mall.imageUrl) return;

    setIsDeletingImage(true);
    try {
      // Delete image file
      if (mall.imageUrl.startsWith('/malls/')) {
        await fetch('/api/upload', {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ url: mall.imageUrl }),
        });
      }

      // Update mall to remove image URL
      const updateResponse = await fetch(`/api/malls/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ imageUrl: null }),
      });

      if (!updateResponse.ok) throw new Error('Failed to update mall');

      toast.success('Mall image deleted successfully');
      setMall({ ...mall, imageUrl: null });
    } catch {
      toast.error('Failed to delete image');
    } finally {
      setIsDeletingImage(false);
      setIsDeleteMallImageDialogOpen(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
      </div>
    );
  }

  if (!mall) {
    return (
      <div className="min-h-screen flex flex-col items-center justify-center gap-4">
        <Building2 className="w-16 h-16 text-muted-foreground" />
        <h1 className="text-2xl font-bold">Mall not found</h1>
        <Link href="/">
          <Button>
            <ArrowLeft className="w-4 h-4 mr-2" />
            Back to Mall List
          </Button>
        </Link>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900">
      {/* Mall-Specific Header Advertisement (with fallback to global) */}
      <AdvertisementBanner position="MALL_HEADER" mallId={id} className="min-h-16" />
      
      {/* SEO Structured Data - only render when data is available */}
      {mall && <MallJsonLd mall={mall} stores={stores} />}
      
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
              {isAuthenticated && (
                <Button
                  variant="ghost"
                  size="sm"
                  onClick={async () => {
                    await fetch('/api/auth/logout', { method: 'POST' });
                    toast.success('Logged out successfully');
                    setIsAuthenticated(false);
                    router.push('/');
                  }}
                >
                  <LogOut className="w-4 h-4 sm:mr-2" />
                  <span className="hidden sm:inline">Logout</span>
                </Button>
              )}
              
              {/* Social Links in Header */}
              {(siteSettings.facebook || siteSettings.twitter || siteSettings.instagram || siteSettings.linkedin || siteSettings.youtube || siteSettings.whatsapp || siteSettings.pinterest) && (
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
              {(siteSettings.facebook || siteSettings.twitter || siteSettings.instagram || siteSettings.linkedin || siteSettings.youtube || siteSettings.whatsapp || siteSettings.pinterest) && (
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
                {isAuthenticated && (
                  <Button
                    variant="ghost"
                    className="w-full justify-start"
                    onClick={async () => {
                      await fetch('/api/auth/logout', { method: 'POST' });
                      toast.success('Logged out successfully');
                      setIsAuthenticated(false);
                      setMobileMenuOpen(false);
                      router.push('/');
                    }}
                  >
                    <LogOut className="w-4 h-4 mr-2" />
                    Logout
                  </Button>
                )}
              </div>
            </nav>
          )}
        </div>
      </header>
      
      {/* Hero Section */}
      <div className="relative h-64 sm:h-80 bg-gradient-to-r from-slate-800 to-slate-900">
        {mall.imageUrl ? (
          <div className="absolute inset-0">
            <Image
              src={mall.imageUrl}
              alt={mall.name}
              fill
              className="object-cover opacity-50"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
          </div>
        ) : (
          <div className="absolute inset-0 flex items-center justify-center">
            <Building2 className="w-24 h-24 text-white/20" />
          </div>
        )}
        
        {/* Image management controls for authenticated users */}
        {isAuthenticated && (
          <div className="absolute top-4 left-1/2 -translate-x-1/2 z-10 flex items-center gap-2">
            <input
              type="file"
              accept="image/*"
              onChange={handleMallImageUpload}
              className="hidden"
              id="mall-image-upload"
              disabled={isUploadingImage}
            />
            <label htmlFor="mall-image-upload">
              <Button
                variant="secondary"
                size="sm"
                asChild
                className="cursor-pointer"
                disabled={isUploadingImage}
              >
                <span>
                  {isUploadingImage ? (
                    <Loader2 className="w-4 h-4 animate-spin" />
                  ) : (
                    <ImagePlus className="w-4 h-4 sm:mr-2" />
                  )}
                  <span className="hidden sm:inline">
                    {mall.imageUrl ? 'Change Image' : 'Add Image'}
                  </span>
                </span>
              </Button>
            </label>
            {mall.imageUrl && (
              <Button
                variant="destructive"
                size="sm"
                onClick={() => setIsDeleteMallImageDialogOpen(true)}
                disabled={isDeletingImage}
              >
                {isDeletingImage ? (
                  <Loader2 className="w-4 h-4 animate-spin" />
                ) : (
                  <Trash2 className="w-4 h-4 sm:mr-2" />
                )}
                <span className="hidden sm:inline">Delete</span>
              </Button>
            )}
          </div>
        )}
        
        <div className="absolute bottom-0 left-0 right-0 p-6 sm:p-8">
          <div className="max-w-7xl mx-auto">
            <Badge
              variant="outline"
              className={
                mall.category === 'existing'
                  ? 'bg-emerald-50 text-emerald-700 border-emerald-200 mb-3'
                  : 'bg-amber-50 text-amber-700 border-amber-200 mb-3'
              }
            >
              {mall.category === 'existing' ? (
                <>
                  <StoreIcon className="w-3 h-3 mr-1" />
                  Existing Mall
                </>
              ) : (
                <>
                  <Sparkles className="w-3 h-3 mr-1" />
                  Upcoming Mall
                </>
              )}
            </Badge>
            <h1 className="text-3xl sm:text-4xl font-bold text-white mb-2">{mall.name}</h1>
            <div className="flex flex-wrap items-center gap-4 text-white/80 text-sm">
              <div className="flex items-center gap-1">
                <MapPin className="w-4 h-4" />
                <span>{mall.address}</span>
              </div>
              {mall.openingHours && (
                <div className="flex items-center gap-1">
                  <Clock className="w-4 h-4" />
                  <span>{mall.openingHours}</span>
                </div>
              )}
              {mall.expectedOpeningDate && (
                <div className="flex items-center gap-1">
                  <Calendar className="w-4 h-4" />
                  <span>Opening: {mall.expectedOpeningDate}</span>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="flex gap-6">
          {/* Left Sidebar Ads - Hidden on small screens */}
          <aside className="hidden xl:block w-48 shrink-0">
            <div className="sticky top-24">
              <SidebarAdvertisements position="MALL_SIDEBAR" mallId={id} />
            </div>
          </aside>

          {/* Main Content */}
          <div className="flex-1 min-w-0">
        {/* Social Share Buttons */}
        <div className="mb-4 flex items-center justify-between">
          <SocialShareButtons 
            title={mall ? `${mall.name} - MallFinder` : 'MallFinder'}
            description={mall?.description || 'Explore this shopping mall'}
            variant="icons"
            size="sm"
          />
        </div>

        {/* Mall Search Section */}
        <Card className="mb-6">
          <CardContent className="p-3 sm:p-4">
            <div className="flex flex-col sm:flex-row gap-2 sm:gap-3">
              <div className="relative flex-1" ref={mallSearchContainerRef}>
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground z-10" />
                <Input
                  placeholder="Search malls by name, state, city, area, or address..."
                  value={mallSearchQuery}
                  onChange={(e) => {
                    setMallSearchQuery(e.target.value);
                    if (e.target.value.trim().length >= 2) {
                      setShowMallSuggestions(true);
                    }
                  }}
                  onFocus={() => {
                    if (mallSearchQuery.trim().length >= 2 && mallSearchSuggestions.length > 0) {
                      setShowMallSuggestions(true);
                    }
                  }}
                  onKeyDown={(e) => {
                    if (e.key === 'Enter') {
                      setShowMallSuggestions(false);
                    }
                    if (e.key === 'Escape') {
                      setShowMallSuggestions(false);
                    }
                  }}
                  className="pl-10 h-9"
                  autoComplete="off"
                />
                
                {/* Search Suggestions Dropdown */}
                {showMallSuggestions && mallSearchSuggestions.length > 0 && (
                  <div className="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg z-50 max-h-80 overflow-y-auto">
                    <div className="p-2 text-xs text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700">
                      Suggestions ({mallSearchSuggestions.length})
                    </div>
                    {mallSearchSuggestions.map((suggestedMall) => (
                      <button
                        key={suggestedMall.id}
                        type="button"
                        className="w-full px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center gap-3 transition-colors"
                        onClick={() => handleMallSuggestionClick(suggestedMall.id)}
                      >
                        <div className="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-md flex-shrink-0 overflow-hidden">
                          {suggestedMall.imageUrl ? (
                            <Image
                              src={suggestedMall.imageUrl}
                              alt={suggestedMall.name}
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
                            {suggestedMall.name}
                          </div>
                          <div className="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {[suggestedMall.area, suggestedMall.city, suggestedMall.state].filter(Boolean).join(', ')}
                          </div>
                        </div>
                        <Badge 
                          variant={suggestedMall.category === 'existing' ? 'default' : 'secondary'}
                          className={`text-xs flex-shrink-0 ${
                            suggestedMall.category === 'existing' 
                              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' 
                              : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                          }`}
                        >
                          {suggestedMall.category === 'existing' ? 'Existing' : 'Upcoming'}
                        </Badge>
                      </button>
                    ))}
                  </div>
                )}
              </div>

              <Button
                onClick={() => {
                  setShowMallSuggestions(false);
                  if (mallSearchSuggestions.length > 0) {
                    router.push(`/mall/${mallSearchSuggestions[0].id}`);
                  }
                }}
                className={`h-9 text-white gap-2 ${
                  mall.category === 'upcoming'
                    ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600'
                    : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
                }`}
              >
                <Search className="w-4 h-4" />
                <span className="hidden sm:inline">Search</span>
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Mall Details Section */}
        <Card className="mb-6">
          <CardHeader>
            <CardTitle>About the Mall</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground mb-4">{mall.description}</p>
            
            {mall.features && mall.features.length > 0 && (
              <div className="mb-4">
                <p className="text-sm font-medium mb-2">Features</p>
                <div className="flex flex-wrap gap-2">
                  {mall.features.map((feature, index) => (
                    <Badge key={index} variant="secondary">
                      {feature}
                    </Badge>
                  ))}
                </div>
              </div>
            )}
            
            {mall.amenities && mall.amenities.length > 0 && (
              <div>
                <p className="text-sm font-medium mb-2">Amenities</p>
                <div className="flex flex-wrap gap-2">
                  {mall.amenities.map((amenity, index) => (
                    <Badge key={index} variant="outline">
                      {amenity}
                    </Badge>
                  ))}
                </div>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Mall Map */}
        {mall.coordinates && (
          <div className="h-64 lg:h-80 rounded-lg overflow-hidden shadow-lg border bg-white mb-6">
            <MallMap
              malls={[mall]}
              selectedMall={mall}
              onMallSelect={() => {}}
            />
          </div>
        )}

        {/* Quick Stats - Compact & Clickable */}
        <div className="grid grid-cols-4 gap-2 mb-4">
          <Card
            className={`cursor-pointer transition-all hover:shadow-md ${statusFilter === 'all' && categoryFilter === 'all' ? `ring-2 ${mall.category === 'upcoming' ? 'ring-amber-500' : 'ring-emerald-500'}` : ''}`}
            onClick={() => { setStatusFilter('all'); setCategoryFilter('all'); }}
          >
            <CardContent className="p-2">
              <div className="flex items-center gap-2">
                <div className={`p-1.5 rounded-md ${mall.category === 'upcoming' ? 'bg-amber-100 dark:bg-amber-900' : 'bg-emerald-100 dark:bg-emerald-900'}`}>
                  <StoreIcon className={`w-3.5 h-3.5 ${mall.category === 'upcoming' ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'}`} />
                </div>
                <div>
                  <p className="text-lg font-bold">{stores.length}</p>
                  <p className="text-[10px] text-muted-foreground leading-tight">Total</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card
            className={`cursor-pointer transition-all hover:shadow-md ${statusFilter === 'OPEN' ? 'ring-2 ring-green-500' : ''}`}
            onClick={() => setStatusFilter(statusFilter === 'OPEN' ? 'all' : 'OPEN')}
          >
            <CardContent className="p-2">
              <div className="flex items-center gap-2">
                <div className="p-1.5 bg-green-100 dark:bg-green-900 rounded-md">
                  <Building2 className="w-3.5 h-3.5 text-green-600 dark:text-green-400" />
                </div>
                <div>
                  <p className="text-lg font-bold text-green-600">{openStores}</p>
                  <p className="text-[10px] text-muted-foreground leading-tight">Open</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card
            className={`cursor-pointer transition-all hover:shadow-md ${statusFilter === 'COMING_SOON' ? 'ring-2 ring-amber-500' : ''}`}
            onClick={() => setStatusFilter(statusFilter === 'COMING_SOON' ? 'all' : 'COMING_SOON')}
          >
            <CardContent className="p-2">
              <div className="flex items-center gap-2">
                <div className="p-1.5 bg-amber-100 dark:bg-amber-900 rounded-md">
                  <Sparkles className="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                  <p className="text-lg font-bold text-amber-600">{comingSoonStores}</p>
                  <p className="text-[10px] text-muted-foreground leading-tight">Soon</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card
            className={`cursor-pointer transition-all hover:shadow-md ${statusFilter === 'CLOSED' ? 'ring-2 ring-red-500' : ''}`}
            onClick={() => setStatusFilter(statusFilter === 'CLOSED' ? 'all' : 'CLOSED')}
          >
            <CardContent className="p-2">
              <div className="flex items-center gap-2">
                <div className="p-1.5 bg-red-100 dark:bg-red-900 rounded-md">
                  <X className="w-3.5 h-3.5 text-red-600 dark:text-red-400" />
                </div>
                <div>
                  <p className="text-lg font-bold text-red-600">{closedStores}</p>
                  <p className="text-[10px] text-muted-foreground leading-tight">Closed</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Active Filter Indicator */}
        {(statusFilter !== 'all' || categoryFilter !== 'all') && (
          <div className="flex items-center gap-2 mb-4 flex-wrap">
            <span className="text-sm text-muted-foreground">Active filters:</span>
            {statusFilter !== 'all' && (
              <Badge
                variant="secondary"
                className="cursor-pointer hover:bg-destructive hover:text-destructive-foreground transition-colors"
                onClick={() => setStatusFilter('all')}
              >
                {statusFilter === 'OPEN' ? 'Open Now' : statusFilter === 'COMING_SOON' ? 'Coming Soon' : 'Closed'}
                <X className="w-3 h-3 ml-1" />
              </Badge>
            )}
            {categoryFilter !== 'all' && (
              <Badge
                variant="secondary"
                className="cursor-pointer hover:bg-destructive hover:text-destructive-foreground transition-colors"
                onClick={() => setCategoryFilter('all')}
              >
                {categoryFilter}
                <X className="w-3 h-3 ml-1" />
              </Badge>
            )}
            <Button
              variant="ghost"
              size="sm"
              className="text-xs h-6"
              onClick={() => {
                setStatusFilter('all');
                setCategoryFilter('all');
              }}
            >
              Clear all
            </Button>
          </div>
        )}

        {/* Stores Section */}
        <Card>
          <CardHeader className="flex flex-row items-center justify-between">
            <CardTitle className="flex items-center gap-2">
              <StoreIcon className="w-5 h-5" />
              Stores Directory
            </CardTitle>
            {isAuthenticated && (
              <Button
                onClick={() => {
                  setEditingStore(null);
                  setIsFormOpen(true);
                }}
                className={mall.category === 'upcoming' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600'}
              >
                <Plus className="w-4 h-4 mr-2" />
                Add Store
              </Button>
            )}
          </CardHeader>
          <CardContent>
            {/* Filters */}
            <div className="flex flex-col sm:flex-row gap-4 mb-6">
              <div className="relative flex-1">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                <Input
                  placeholder="Search stores by name, category, or floor..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && setCurrentStorePage(1)}
                  className="pl-10"
                />
              </div>

              <Button
                onClick={() => setCurrentStorePage(1)}
                className={`text-white gap-2 ${
                  mall.category === 'upcoming'
                    ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600'
                    : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'
                }`}
              >
                <Search className="w-4 h-4" />
                <span className="hidden sm:inline">Search</span>
              </Button>
              
              <Select value={categoryFilter} onValueChange={setCategoryFilter}>
                <SelectTrigger className="w-full sm:w-48" data-category-filter>
                  <SelectValue placeholder="All Categories" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Categories</SelectItem>
                  {categoryOptions.map((category) => (
                    <SelectItem key={category} value={category}>
                      {category}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              <Select value={statusFilter} onValueChange={setStatusFilter}>
                <SelectTrigger className="w-full sm:w-40">
                  <SelectValue placeholder="All Status" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="all">All Status</SelectItem>
                  <SelectItem value="OPEN">Open</SelectItem>
                  <SelectItem value="COMING_SOON">Coming Soon</SelectItem>
                  <SelectItem value="CLOSED">Closed</SelectItem>
                </SelectContent>
              </Select>
            </div>

            {/* Stores List */}
            {storesLoading ? (
              <div className="flex items-center justify-center py-12">
                <Loader2 className={`w-8 h-8 animate-spin ${mall.category === 'upcoming' ? 'text-amber-500' : 'text-emerald-500'}`} />
              </div>
            ) : filteredStores.length === 0 ? (
              <div className="text-center py-12">
                <StoreIcon className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                <h3 className="text-lg font-semibold mb-2">No stores found</h3>
                <p className="text-muted-foreground mb-4">
                  {searchQuery || categoryFilter !== 'all' || statusFilter !== 'all'
                    ? 'Try adjusting your filters'
                    : 'Get started by adding the first store'}
                </p>
                {!searchQuery && categoryFilter === 'all' && statusFilter === 'all' && isAuthenticated && (
                  <Button
                    onClick={() => setIsFormOpen(true)}
                    className={mall.category === 'upcoming' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600'}
                  >
                    <Plus className="w-4 h-4 mr-2" />
                    Add First Store
                  </Button>
                )}
              </div>
            ) : (
              <>
                <Tabs defaultValue="all" className="w-full">
                  <TabsList className="mb-4 flex-wrap h-auto gap-1">
                    <TabsTrigger value="all" className="text-xs sm:text-sm">All ({filteredStores.length})</TabsTrigger>
                    {Object.entries(storesByCategory).map(([category, categoryStores]) => (
                      <TabsTrigger key={category} value={category} className="text-xs sm:text-sm">
                        {category} ({categoryStores.length})
                      </TabsTrigger>
                    ))}
                  </TabsList>

                  <TabsContent value="all">
                    <div className="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                      <AnimatePresence mode="popLayout">
                        {paginatedStores.map((store) => (
                          <div key={store.id} className="relative group">
                            <StoreCard
                              store={store}
                              isUpcoming={mall.category === 'upcoming'}
                              onClick={() => {
                                if (isAuthenticated) {
                                  setEditingStore(store);
                                  setIsFormOpen(true);
                                }
                              }}
                            />
                            {isAuthenticated && (
                              <div className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                <Button
                                  variant="secondary"
                                  size="icon"
                                  className="h-8 w-8"
                                  onClick={(e) => {
                                    e.stopPropagation();
                                    setEditingStore(store);
                                    setIsFormOpen(true);
                                  }}
                                >
                                  <Pencil className="w-4 h-4" />
                                </Button>
                                <Button
                                  variant="destructive"
                                  size="icon"
                                  className="h-8 w-8"
                                  onClick={(e) => {
                                    e.stopPropagation();
                                    setStoreToDelete(store);
                                    setIsDeleteDialogOpen(true);
                                  }}
                                >
                                  <Trash2 className="w-4 h-4" />
                                </Button>
                              </div>
                            )}
                          </div>
                        ))}
                      </AnimatePresence>
                    </div>
                    
                    {/* Pagination Controls */}
                    {totalFilteredPages > 1 && (
                      <div className="flex items-center justify-center gap-2 mt-6 pt-4 border-t">
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => setCurrentStorePage(prev => Math.max(1, prev - 1))}
                          disabled={currentStorePage === 1}
                        >
                          <ChevronLeft className="w-4 h-4 mr-1" />
                          Previous
                        </Button>
                        
                        <div className="flex items-center gap-1">
                          {Array.from({ length: Math.min(5, totalFilteredPages) }, (_, i) => {
                            let pageNum;
                            if (totalFilteredPages <= 5) {
                              pageNum = i + 1;
                            } else if (currentStorePage <= 3) {
                              pageNum = i + 1;
                            } else if (currentStorePage >= totalFilteredPages - 2) {
                              pageNum = totalFilteredPages - 4 + i;
                            } else {
                              pageNum = currentStorePage - 2 + i;
                            }
                            
                            return (
                              <Button
                                key={pageNum}
                                variant={currentStorePage === pageNum ? "default" : "outline"}
                                size="sm"
                                className={`w-8 h-8 p-0 ${currentStorePage === pageNum ? (mall.category === 'upcoming' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600') : ''}`}
                                onClick={() => setCurrentStorePage(pageNum)}
                              >
                                {pageNum}
                              </Button>
                            );
                          })}
                        </div>
                        
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => setCurrentStorePage(prev => Math.min(totalFilteredPages, prev + 1))}
                          disabled={currentStorePage === totalFilteredPages}
                        >
                          Next
                          <ChevronRight className="w-4 h-4 ml-1" />
                        </Button>
                      </div>
                    )}
                    
                    {/* Page indicator */}
                    {totalFilteredPages > 1 && (
                      <p className="text-center text-sm text-muted-foreground mt-2">
                        Showing {((currentStorePage - 1) * STORES_PER_PAGE) + 1} - {Math.min(currentStorePage * STORES_PER_PAGE, filteredStores.length)} of {filteredStores.length} stores
                      </p>
                    )}
                  </TabsContent>

                  {Object.entries(storesByCategory).map(([category, categoryStores]) => {
                    const categoryTotalPages = Math.ceil(categoryStores.length / STORES_PER_PAGE);
                    const paginatedCategoryStores = categoryStores.slice(
                      (currentStorePage - 1) * STORES_PER_PAGE,
                      currentStorePage * STORES_PER_PAGE
                    );
                    
                    return (
                      <TabsContent key={category} value={category}>
                        <div className="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                          <AnimatePresence mode="popLayout">
                            {paginatedCategoryStores.map((store) => (
                              <div key={store.id} className="relative group">
                                <StoreCard
                                  store={store}
                                  isUpcoming={mall.category === 'upcoming'}
                                  onClick={() => {
                                    if (isAuthenticated) {
                                      setEditingStore(store);
                                      setIsFormOpen(true);
                                    }
                                  }}
                                />
                                {isAuthenticated && (
                                  <div className="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                                    <Button
                                      variant="secondary"
                                      size="icon"
                                      className="h-8 w-8"
                                      onClick={(e) => {
                                        e.stopPropagation();
                                        setEditingStore(store);
                                        setIsFormOpen(true);
                                      }}
                                    >
                                      <Pencil className="w-4 h-4" />
                                    </Button>
                                    <Button
                                      variant="destructive"
                                      size="icon"
                                      className="h-8 w-8"
                                      onClick={(e) => {
                                        e.stopPropagation();
                                        setStoreToDelete(store);
                                        setIsDeleteDialogOpen(true);
                                      }}
                                    >
                                      <Trash2 className="w-4 h-4" />
                                    </Button>
                                  </div>
                                )}
                              </div>
                            ))}
                          </AnimatePresence>
                        </div>
                        
                        {/* Pagination Controls for Category */}
                        {categoryTotalPages > 1 && (
                          <>
                            <div className="flex items-center justify-center gap-2 mt-6 pt-4 border-t">
                              <Button
                                variant="outline"
                                size="sm"
                                onClick={() => setCurrentStorePage(prev => Math.max(1, prev - 1))}
                                disabled={currentStorePage === 1}
                              >
                                <ChevronLeft className="w-4 h-4 mr-1" />
                                Previous
                              </Button>
                              
                              <div className="flex items-center gap-1">
                                {Array.from({ length: Math.min(5, categoryTotalPages) }, (_, i) => {
                                  let pageNum;
                                  if (categoryTotalPages <= 5) {
                                    pageNum = i + 1;
                                  } else if (currentStorePage <= 3) {
                                    pageNum = i + 1;
                                  } else if (currentStorePage >= categoryTotalPages - 2) {
                                    pageNum = categoryTotalPages - 4 + i;
                                  } else {
                                    pageNum = currentStorePage - 2 + i;
                                  }
                                  
                                  return (
                                    <Button
                                      key={pageNum}
                                      variant={currentStorePage === pageNum ? "default" : "outline"}
                                      size="sm"
                                      className={`w-8 h-8 p-0 ${currentStorePage === pageNum ? (mall.category === 'upcoming' ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600') : ''}`}
                                      onClick={() => setCurrentStorePage(pageNum)}
                                    >
                                      {pageNum}
                                    </Button>
                                  );
                                })}
                              </div>
                              
                              <Button
                                variant="outline"
                                size="sm"
                                onClick={() => setCurrentStorePage(prev => Math.min(categoryTotalPages, prev + 1))}
                                disabled={currentStorePage === categoryTotalPages}
                              >
                                Next
                                <ChevronRight className="w-4 h-4 ml-1" />
                              </Button>
                            </div>
                            
                            <p className="text-center text-sm text-muted-foreground mt-2">
                              Showing {((currentStorePage - 1) * STORES_PER_PAGE) + 1} - {Math.min(currentStorePage * STORES_PER_PAGE, categoryStores.length)} of {categoryStores.length} stores
                            </p>
                          </>
                        )}
                      </TabsContent>
                    );
                  })}
                </Tabs>
              </>
            )}
          </CardContent>
        </Card>
          </div>

          {/* Right Sidebar Ads - Hidden on small screens */}
          <aside className="hidden xl:block w-48 shrink-0">
            <div className="sticky top-24">
              <SidebarAdvertisements position="RIGHT_SIDEBAR" mallId={id} />
            </div>
          </aside>
        </div>
      </main>

      {/* Store Form Dialog */}
      <StoreForm
        open={isFormOpen}
        onOpenChange={setIsFormOpen}
        onSubmit={handleFormSubmit}
        store={editingStore}
        isSubmitting={isSubmitting}
        categories={categories}
        onCategoryAdded={fetchCategories}
        isUpcoming={mall.category === 'upcoming'}
      />

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete Store</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete <strong>{storeToDelete?.name}</strong>?
              This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={handleDeleteConfirm}
              className="bg-destructive text-destructive-foreground hover:bg-destructive/90"
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* Explore Other Malls Section */}
      <section className={`py-10 sm:py-12 ${mall.category === 'upcoming' ? 'bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 dark:from-slate-800 dark:via-slate-800 dark:to-slate-900' : 'bg-gradient-to-br from-emerald-50 via-teal-50 to-cyan-50 dark:from-slate-800 dark:via-slate-800 dark:to-slate-900'}`}>
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          {/* Section Header */}
          <div className="text-center mb-8">
            <h2 className="text-2xl sm:text-3xl font-bold mb-2">
              <span className={`bg-clip-text text-transparent ${mall.category === 'upcoming' ? 'bg-gradient-to-r from-amber-600 to-orange-600' : 'bg-gradient-to-r from-emerald-600 to-teal-600'}`}>
                Explore Other Malls
              </span>
            </h2>
            <p className="text-muted-foreground text-sm sm:text-base">
              Discover more shopping destinations near you
            </p>
          </div>

          {/* Mall Carousel */}
          {suggestedMalls.length > 0 ? (
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
                {suggestedMalls.map((suggestedMall) => (
                  <Link
                    key={suggestedMall.id}
                    href={`/mall/${suggestedMall.id}`}
                    className="flex-shrink-0 w-64 sm:w-72 group"
                  >
                    <Card className={`overflow-hidden h-full transition-all duration-300 hover:shadow-xl hover:scale-[1.02] ${
                      suggestedMall.category === 'upcoming'
                        ? 'border-2 border-amber-300 dark:border-amber-700'
                        : 'border-2 border-emerald-300 dark:border-emerald-700'
                    }`}>
                      {/* Image */}
                      <div className="relative h-36 sm:h-40 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-800">
                        {suggestedMall.imageUrl ? (
                          <Image
                            src={suggestedMall.imageUrl}
                            alt={suggestedMall.name}
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
                            suggestedMall.category === 'upcoming'
                              ? 'bg-amber-500 text-white'
                              : 'bg-emerald-500 text-white'
                          }`}>
                            {suggestedMall.category === 'upcoming' ? 'Upcoming' : 'Existing'}
                          </span>
                        </div>
                      </div>

                      {/* Content */}
                      <CardContent className="p-3">
                        <h3 className="font-semibold text-sm sm:text-base truncate mb-1">
                          {suggestedMall.name}
                        </h3>
                        <p className="text-xs text-muted-foreground truncate mb-2">
                          {suggestedMall.area || suggestedMall.address}
                        </p>
                        {suggestedMall.description && (
                          <p className="text-xs text-muted-foreground line-clamp-2">
                            {suggestedMall.description}
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
              <p className="text-muted-foreground">No other malls to explore</p>
            </div>
          )}

          {/* View All Malls Button */}
          <div className="text-center mt-6">
            <Link href="/">
              <Button className={`text-white ${mall.category === 'upcoming' ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600' : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600'}`}>
                View All Malls
              </Button>
            </Link>
          </div>
        </div>
      </section>

      {/* Mall-Specific Footer Advertisement (with fallback to global) */}
      <AdvertisementBanner position="MALL_FOOTER" mallId={id} />

      {/* Footer */}
      <footer className="bg-white dark:bg-slate-900 border-t">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            {/* Brand */}
            <div>
              <Link href="/" className="flex items-center gap-2 mb-4">
                {siteSettings.siteLogo ? (
                  <Image 
                    src={siteSettings.siteLogo} 
                    alt={siteSettings.siteName} 
                    width={40} 
                    height={40}
                    className="object-contain"
                  />
                ) : (
                  <div className="bg-gradient-to-r from-emerald-500 to-teal-500 p-2 rounded-lg">
                    <Building2 className="w-5 h-5 text-white" />
                  </div>
                )}
                <span className="font-semibold">{siteSettings.siteName}</span>
              </Link>
              <p className="text-sm text-muted-foreground mb-4">
                {siteSettings.siteTagline}
              </p>
              
              {/* Social Links */}
              {(siteSettings.facebook || siteSettings.twitter || siteSettings.instagram || siteSettings.linkedin || siteSettings.youtube || siteSettings.whatsapp || siteSettings.pinterest) && (
                <div>
                  <p className="text-sm font-medium mb-3">Follow us:</p>
                  <SocialLinks links={siteSettings} iconSize="sm" />
                </div>
              )}
            </div>

            {/* Quick Links */}
            <div>
              <h4 className="font-semibold mb-4">Quick Links</h4>
              <nav className="flex flex-col gap-2">
                <Link href="/" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Home
                </Link>
                <Link href="/about" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  About Us
                </Link>
                <Link href="/contact" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Contact Us
                </Link>
              </nav>
            </div>

            {/* Categories */}
            <div>
              <h4 className="font-semibold mb-4">Categories</h4>
              <nav className="flex flex-col gap-2">
                <Link href="/?filter=existing" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Existing Malls
                </Link>
                <Link href="/?filter=upcoming" className="text-sm text-muted-foreground hover:text-foreground transition-colors">
                  Upcoming Malls
                </Link>
              </nav>
            </div>

            {/* Contact Info */}
            <div>
              <h4 className="font-semibold mb-4">Current Mall</h4>
              <div className="flex flex-col gap-2 text-sm text-muted-foreground">
                <p className="font-medium text-foreground">{mall.name}</p>
                {mall.address && (
                  <p className="flex items-center gap-1">
                    <MapPin className="w-3 h-3" />
                    {mall.address}
                  </p>
                )}
                {mall.openingHours && (
                  <p className="flex items-center gap-1">
                    <Clock className="w-3 h-3" />
                    {mall.openingHours}
                  </p>
                )}
              </div>
            </div>
          </div>

          <div className="border-t mt-8 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p className="text-sm text-muted-foreground">
              © {new Date().getFullYear()} {siteSettings.siteName}. All rights reserved.
            </p>
            <div className="flex items-center gap-4 text-sm text-muted-foreground">
              <Link href="/" className="hover:text-foreground transition-colors">
                Home
              </Link>
              <Link href="/about" className="hover:text-foreground transition-colors">
                About
              </Link>
              <Link href="/contact" className="hover:text-foreground transition-colors">
                Contact
              </Link>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}

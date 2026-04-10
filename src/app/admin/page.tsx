'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { useForm } from 'react-hook-form';
import dynamic from 'next/dynamic';
import Image from 'next/image';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
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
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
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
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { MallCard } from '@/components/mall/MallCard';
import { SiteHeader } from '@/components/layout/SiteHeader';
import {
  Search,
  Plus,
  Store,
  Sparkles,
  MoreHorizontal,
  Pencil,
  Trash2,
  MapPin,
  Building2,
  Clock,
  Calendar,
  Loader2,
  Eye,
  ArrowUpDown,
  ArrowUp,
  ArrowDown,
  Settings,
  MapPinned,
  Tag,
  LogOut,
  User,
  FileText,
  Star,
  Coffee,
  X,
  Megaphone,
  Building,
  ChevronLeft,
  ChevronRight,
  Filter,
  RefreshCw,
  ExternalLink,
} from 'lucide-react';
import { toast } from 'sonner';
import { motion, AnimatePresence } from 'framer-motion';
import { Mall } from '@/types/mall';
import type { Area } from '@/types/area';
import type { StoreCategory } from '@/types/category';

// Types for features and amenities
interface Feature {
  id: string;
  name: string;
  icon?: string | null;
}

interface Amenity {
  id: string;
  name: string;
  icon?: string | null;
}

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
  state?: State;
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

// Dynamic import for CoordinatePickerMap
const CoordinatePickerMap = dynamic(
  () => import('@/components/mall/CoordinatePickerMap').then((mod) => mod.CoordinatePickerMap),
  {
    ssr: false,
    loading: () => (
      <div className="w-full h-full bg-muted rounded-lg flex items-center justify-center">
        <div className="text-muted-foreground">Loading map...</div>
      </div>
    ),
  }
);

interface MallFormData {
  name: string;
  description: string;
  address: string;
  area: string;
  city: string;
  state: string;
  latitude: string;
  longitude: string;
  imageUrl: string;
  status: 'EXISTING' | 'UPCOMING';
  openingHours: string;
  expectedOpeningDate: string;
  features: string;
  amenities: string;
  // SEO Fields
  metaTitle: string;
  metaDescription: string;
  keywords: string;
  ogImage: string;
  canonicalUrl: string;
}

const defaultFormData: MallFormData = {
  name: '',
  description: '',
  address: '',
  area: '',
  city: '',
  state: '',
  latitude: '',
  longitude: '',
  imageUrl: '',
  status: 'EXISTING',
  openingHours: '',
  expectedOpeningDate: '',
  features: '',
  amenities: '',
  // SEO Fields
  metaTitle: '',
  metaDescription: '',
  keywords: '',
  ogImage: '',
  canonicalUrl: '',
};

export default function CMSDashboard() {
  const router = useRouter();
  const [malls, setMalls] = useState<Mall[]>([]);
  const [areas, setAreas] = useState<Area[]>([]);
  const [categories, setCategories] = useState<StoreCategory[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [sortField, setSortField] = useState<'name' | 'status' | 'area' | 'city' | 'state'>('name');
  const [sortDirection, setSortDirection] = useState<'asc' | 'desc'>('asc');
  const [mallViewMode, setMallViewMode] = useState<'table' | 'grouped'>('table');
  const [selectedMall, setSelectedMall] = useState<Mall | null>(null);
  const [viewMode, setViewMode] = useState<'cms' | 'preview'>('cms');
  const [showMap, setShowMap] = useState(true);
  
  // Authentication state
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [authUser, setAuthUser] = useState<{ email: string; name: string | null } | null>(null);
  const [authChecking, setAuthChecking] = useState(true);
  
  // Dialog states
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [mallToDelete, setMallToDelete] = useState<Mall | null>(null);
  const [editingMall, setEditingMall] = useState<Mall | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  
  // Site settings states
  const [siteSettings, setSiteSettings] = useState({
    siteName: 'MallFinder',
    siteTagline: 'Discover Shopping Destinations',
    siteLogo: '',
  });
  
  // Preview mode states
  const [previewPage, setPreviewPage] = useState(1);
  const [activePreviewTab, setActivePreviewTab] = useState<'existing' | 'upcoming'>('existing');
  const [previewRefreshKey, setPreviewRefreshKey] = useState(0);
  const [showPreviewMap, setShowPreviewMap] = useState(true);
  const ITEMS_PER_PAGE = 12;
  
  // Map location filters for preview
  const [mapStateFilter, setMapStateFilter] = useState<string>('all');
  const [mapCityFilter, setMapCityFilter] = useState<string>('all');
  const [mapAreaFilter, setMapAreaFilter] = useState<string>('all');
  
  // Mall management filters
  const [mallStateFilter, setMallStateFilter] = useState<string>('all');
  const [mallCityFilter, setMallCityFilter] = useState<string>('all');
  const [mallAreaFilter, setMallAreaFilter] = useState<string>('all');
  
  // Features & Amenities for dropdowns
  const [features, setFeatures] = useState<Feature[]>([]);
  const [amenities, setAmenities] = useState<Amenity[]>([]);
  
  // Location management states (State > City > Area)
  const [states, setStates] = useState<State[]>([]);
  
  const form = useForm<MallFormData>({
    defaultValues: defaultFormData,
  });
  
  const watchStatus = form.watch('status');
  const watchState = form.watch('state');
  const watchCity = form.watch('city');
  
  // Fetch malls from API
  const fetchMalls = useCallback(async () => {
    try {
      setLoading(true);
      const response = await fetch('/api/malls');
      if (!response.ok) throw new Error('Failed to fetch malls');
      const data = await response.json();
      setMalls(data);
    } catch {
      toast.error('Failed to load malls');
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
  
  // Fetch categories from API
  const fetchCategories = useCallback(async () => {
    try {
      const response = await fetch('/api/categories');
      if (!response.ok) throw new Error('Failed to fetch categories');
      const data = await response.json();
      setCategories(data);
    } catch {
      console.error('Failed to load categories');
    }
  }, []);
  
  // Fetch features from API
  const fetchFeatures = useCallback(async () => {
    try {
      const response = await fetch('/api/features');
      if (!response.ok) throw new Error('Failed to fetch features');
      const data = await response.json();
      setFeatures(data);
    } catch {
      console.error('Failed to load features');
    }
  }, []);
  
  // Fetch amenities from API
  const fetchAmenities = useCallback(async () => {
    try {
      const response = await fetch('/api/amenities');
      if (!response.ok) throw new Error('Failed to fetch amenities');
      const data = await response.json();
      setAmenities(data);
    } catch {
      console.error('Failed to load amenities');
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
        });
      }
    } catch {
      console.error('Failed to load settings');
    }
  }, []);
  
  useEffect(() => {
    fetchMalls();
    fetchAreas();
    fetchCategories();
    fetchFeatures();
    fetchAmenities();
    fetchSettings();
    fetchStates();
  }, [fetchMalls, fetchAreas, fetchCategories, fetchFeatures, fetchAmenities, fetchSettings, fetchStates]);
  
  // Check authentication
  useEffect(() => {
    const checkAuth = async () => {
      try {
        const response = await fetch('/api/auth/session');
        const data = await response.json();
        if (data.authenticated) {
          setIsAuthenticated(true);
          setAuthUser(data.user);
        } else {
          // Redirect to login page if not authenticated
          router.push('/admin/login');
        }
      } catch {
        // Redirect to login page on error
        router.push('/admin/login');
      } finally {
        setAuthChecking(false);
      }
    };
    checkAuth();
  }, [router]);
  
  // Logout function
  const handleLogout = async () => {
    try {
      await fetch('/api/auth/logout', { method: 'POST' });
      toast.success('Logged out successfully');
      router.push('/admin/login');
    } catch {
      toast.error('Failed to logout');
    }
  };
  
  // Reset preview page when filters change
  useEffect(() => {
    setPreviewPage(1);
  }, [searchQuery, statusFilter]);

  // Refresh all data when switching to preview mode
  useEffect(() => {
    if (viewMode === 'preview') {
      fetchMalls();
      fetchAreas();
      fetchCategories();
      fetchFeatures();
      fetchAmenities();
      fetchSettings();
      setPreviewRefreshKey(prev => prev + 1);
    }
  }, [viewMode]);

  // Get cities for selected state
  const citiesForState = states.find(s => s.name === watchState)?.cities || [];
  
  // Get areas for selected city
  const areasForCity = citiesForState.find(c => c.name === watchCity)?.areas || [];
  
  // Get cities for mall management state filter
  const citiesForMallState = states.find(s => s.name === mallStateFilter)?.cities || [];
  
  // Get areas for mall management city filter
  const areasForMallCity = citiesForMallState.find(c => c.name === mallCityFilter)?.areas || [];
  
  // Reset city and area when state changes
  useEffect(() => {
    setMallCityFilter('all');
    setMallAreaFilter('all');
  }, [mallStateFilter]);
  
  // Reset area when city changes
  useEffect(() => {
    setMallAreaFilter('all');
  }, [mallCityFilter]);
  
  // Filter malls based on search, status, and location
  const filteredMalls = malls.filter((mall) => {
    const matchesSearch =
      mall.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      mall.address.toLowerCase().includes(searchQuery.toLowerCase()) ||
      mall.area.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesStatus =
      statusFilter === 'all' || mall.category === statusFilter;
    const matchesState =
      mallStateFilter === 'all' || mall.state === mallStateFilter;
    const matchesCity =
      mallCityFilter === 'all' || mall.city === mallCityFilter;
    const matchesArea =
      mallAreaFilter === 'all' || mall.area === mallAreaFilter;
    return matchesSearch && matchesStatus && matchesState && matchesCity && matchesArea;
  });

  // Sort malls
  const sortedMalls = [...filteredMalls].sort((a, b) => {
    let comparison = 0;
    if (sortField === 'name') {
      comparison = a.name.localeCompare(b.name);
    } else if (sortField === 'status') {
      comparison = a.category.localeCompare(b.category);
    } else if (sortField === 'area') {
      comparison = a.area.localeCompare(b.area);
    } else if (sortField === 'city') {
      comparison = (a.city || '').localeCompare(b.city || '');
    } else if (sortField === 'state') {
      comparison = (a.state || '').localeCompare(b.state || '');
    }
    return sortDirection === 'asc' ? comparison : -comparison;
  });

  // Handle sort toggle
  const handleSort = (field: 'name' | 'status' | 'area' | 'city' | 'state') => {
    if (sortField === field) {
      setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
    } else {
      setSortField(field);
      setSortDirection('asc');
    }
  };

  // Group malls by state and city
  const groupedMalls = sortedMalls.reduce((acc, mall) => {
    const state = mall.state?.trim() || 'Unknown State';
    const city = mall.city?.trim() || 'Unknown City';
    
    if (!acc[state]) {
      acc[state] = {};
    }
    if (!acc[state][city]) {
      acc[state][city] = [];
    }
    acc[state][city].push(mall);
    return acc;
  }, {} as Record<string, Record<string, Mall[]>>);
  
  // Statistics
  const totalMalls = malls.length;
  const existingMalls = malls.filter((m) => m.category === 'existing').length;
  const upcomingMalls = malls.filter((m) => m.category === 'upcoming').length;
  
  // Areas for filter (from database or mall data as fallback)
  const areaNames = areas.length > 0 
    ? areas.map(a => a.name) 
    : [...new Set(malls.map((m) => m.area))].filter(Boolean);
  
  // Map filter logic for preview mode
  const citiesForMapState = states.find(s => s.id === mapStateFilter || s.name === mapStateFilter)?.cities || [];
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
  
  // Malls for map - based on status and location filter
  const mapMalls = malls.filter((m) => {
    const matchesStatus = activePreviewTab === 'upcoming'
      ? m.category === 'upcoming'
      : m.category === 'existing';
    
    if (!m.coordinates?.lat || !m.coordinates?.lng) return false;
    
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
  
  // Open form for new mall
  const handleAddNew = () => {
    setEditingMall(null);
    form.reset(defaultFormData);
    setIsFormOpen(true);
  };
  
  // Open form for editing
  const handleEdit = (mall: Mall) => {
    setEditingMall(mall);
    form.reset({
      name: mall.name,
      description: mall.description || '',
      address: mall.address || '',
      area: mall.area || '',
      city: mall.city || '',
      state: mall.state || '',
      latitude: mall.coordinates?.lat?.toString() || '',
      longitude: mall.coordinates?.lng?.toString() || '',
      imageUrl: mall.imageUrl || '',
      status: (mall.status || mall.category?.toUpperCase()) as 'EXISTING' | 'UPCOMING',
      openingHours: mall.openingHours || '',
      expectedOpeningDate: mall.expectedOpeningDate || '',
      features: mall.features?.join(', ') || '',
      amenities: mall.amenities?.join(', ') || '',
      // SEO Fields
      metaTitle: mall.metaTitle || '',
      metaDescription: mall.metaDescription || '',
      keywords: mall.keywords || '',
      ogImage: mall.ogImage || '',
      canonicalUrl: mall.canonicalUrl || '',
    });
    setIsFormOpen(true);
  };
  
  // Handle delete confirmation
  const handleDeleteClick = (mall: Mall) => {
    setMallToDelete(mall);
    setIsDeleteDialogOpen(true);
  };
  
  // Confirm delete
  const handleDeleteConfirm = async () => {
    if (!mallToDelete) return;
    
    try {
      const response = await fetch(`/api/malls/${mallToDelete.id}`, {
        method: 'DELETE',
      });
      
      if (!response.ok) throw new Error('Failed to delete mall');
      
      toast.success('Mall deleted successfully');
      fetchMalls();
    } catch {
      toast.error('Failed to delete mall');
    } finally {
      setIsDeleteDialogOpen(false);
      setMallToDelete(null);
    }
  };
  
  // Handle form submission
  const onSubmit = async (data: MallFormData) => {
    setIsSubmitting(true);
    
    try {
      const payload = {
        name: data.name,
        description: data.description || null,
        address: data.address || null,
        area: data.area || null,
        city: data.city?.trim() || null,
        state: data.state?.trim() || null,
        latitude: data.latitude ? parseFloat(data.latitude) : null,
        longitude: data.longitude ? parseFloat(data.longitude) : null,
        imageUrl: data.imageUrl || null,
        status: data.status,
        openingHours: data.openingHours || null,
        expectedOpeningDate: data.expectedOpeningDate || null,
        features: data.features ? data.features.split(',').map((f) => f.trim()) : [],
        amenities: data.amenities ? data.amenities.split(',').map((a) => a.trim()) : [],
        // SEO Fields
        metaTitle: data.metaTitle || null,
        metaDescription: data.metaDescription || null,
        keywords: data.keywords || null,
        ogImage: data.ogImage || null,
        canonicalUrl: data.canonicalUrl || null,
      };
      
      if (editingMall) {
        // Update existing mall
        const response = await fetch(`/api/malls/${editingMall.id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });
        
        if (!response.ok) throw new Error('Failed to update mall');
        
        toast.success('Mall updated successfully');
      } else {
        // Create new mall
        const response = await fetch('/api/malls', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });
        
        if (!response.ok) throw new Error('Failed to create mall');
        
        toast.success('Mall created successfully');
      }
      
      setIsFormOpen(false);
      fetchMalls();
    } catch {
      toast.error(editingMall ? 'Failed to update mall' : 'Failed to create mall');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Show loading while checking authentication
  if (authChecking) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 flex items-center justify-center">
        <div className="flex flex-col items-center gap-4">
          <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
          <p className="text-muted-foreground">Loading...</p>
        </div>
      </div>
    );
  }

  // If not authenticated, show loading while redirecting
  if (!isAuthenticated) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 flex items-center justify-center">
        <div className="flex flex-col items-center gap-4">
          <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
          <p className="text-muted-foreground">Redirecting...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900">
      {/* Header */}
      <header className="bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center h-16">
            <div className="flex items-center gap-3">
              <Building2 className="w-8 h-8 text-emerald-500" />
              <div>
                <h1 className="text-xl font-bold text-slate-900 dark:text-white">MallFinder CMS</h1>
                <p className="text-xs text-slate-500 dark:text-slate-400">Content Management System</p>
              </div>
            </div>
            
            <div className="flex items-center gap-3">
              {/* View Mode Toggle */}
              <div className="flex bg-slate-100 dark:bg-slate-800 rounded-lg p-1">
                <button
                  onClick={() => setViewMode('cms')}
                  className={`px-3 py-1.5 rounded-md text-sm font-medium transition-colors ${
                    viewMode === 'cms'
                      ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                  }`}
                >
                  CMS
                </button>
                <button
                  onClick={() => setViewMode('preview')}
                  className={`px-3 py-1.5 rounded-md text-sm font-medium transition-colors flex items-center gap-1 ${
                    viewMode === 'preview'
                      ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                  }`}
                >
                  <Eye className="w-4 h-4" />
                  Preview
                </button>
              </div>
              
              {authUser && (
                <div className="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                  <User className="w-4 h-4" />
                  <span>{authUser.name || authUser.email}</span>
                </div>
              )}
              
              <Button variant="outline" size="sm" onClick={handleLogout}>
                <LogOut className="w-4 h-4 mr-2" />
                Logout
              </Button>
            </div>
          </div>
        </div>
      </header>

      {viewMode === 'preview' ? (
        /* Preview Mode */
        <div className="min-h-screen">
          <SiteHeader
            siteName={siteSettings.siteName}
            siteTagline={siteSettings.siteTagline}
            siteLogo={siteSettings.siteLogo}
          />
          
          {/* Preview Banner */}
          <div className="bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800 px-4 py-2">
            <div className="max-w-7xl mx-auto flex items-center justify-between">
              <div className="flex items-center gap-2 text-amber-700 dark:text-amber-400 text-sm">
                <Eye className="w-4 h-4" />
                <span>Preview Mode - This is how your site looks to visitors</span>
              </div>
              <Link href="/" target="_blank" rel="noopener noreferrer">
                <Button variant="outline" size="sm" className="gap-2">
                  <ExternalLink className="w-4 h-4" />
                  Open Full Site
                </Button>
              </Link>
            </div>
          </div>
          
          <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {/* Map Section Header & Filters */}
            {showPreviewMap && (
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
                  <div className="flex items-center gap-3">
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
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => setShowPreviewMap(false)}
                      className="text-xs"
                    >
                      Hide Map
                    </Button>
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
            
            {!showPreviewMap && (
              <Button
                variant="outline"
                size="sm"
                onClick={() => setShowPreviewMap(true)}
                className="mb-4 gap-2"
              >
                <MapPin className="w-4 h-4" />
                Show Map
              </Button>
            )}
            
            {/* Map */}
            {showPreviewMap && (
              <motion.div
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                className="h-[300px] sm:h-[400px] rounded-lg overflow-hidden shadow-lg border bg-white mb-6"
              >
                <MallMap
                  malls={mapMalls}
                  selectedMall={null}
                  onMallSelect={() => {}}
                />
              </motion.div>
            )}
            
            <Tabs value={activePreviewTab} onValueChange={(v) => setActivePreviewTab(v as 'existing' | 'upcoming')}>
              <TabsList className="mb-6 gap-2 bg-transparent p-0 h-auto">
                <TabsTrigger 
                  value="existing" 
                  className="flex items-center gap-2 data-[state=active]:bg-gradient-to-r data-[state=active]:from-emerald-500 data-[state=active]:to-teal-500 data-[state=active]:text-white data-[state=inactive]:bg-white data-[state=inactive]:dark:bg-slate-800 data-[state=inactive]:border-2 data-[state=inactive]:border-emerald-200 data-[state=inactive]:dark:border-emerald-800 data-[state=inactive]:text-emerald-600 data-[state=inactive]:dark:text-emerald-400 px-4 py-2 rounded-md transition-all"
                >
                  <Store className="w-4 h-4" />
                  Existing Malls ({existingMalls})
                </TabsTrigger>
                <TabsTrigger 
                  value="upcoming" 
                  className="flex items-center gap-2 data-[state=active]:bg-gradient-to-r data-[state=active]:from-amber-500 data-[state=active]:to-orange-500 data-[state=active]:text-white data-[state=inactive]:bg-white data-[state=inactive]:dark:bg-slate-800 data-[state=inactive]:border-2 data-[state=inactive]:border-amber-200 data-[state=inactive]:dark:border-amber-800 data-[state=inactive]:text-amber-600 data-[state=inactive]:dark:text-amber-400 px-4 py-2 rounded-md transition-all"
                >
                  <Sparkles className="w-4 h-4" />
                  Upcoming Malls ({upcomingMalls})
                </TabsTrigger>
              </TabsList>

              <TabsContent value="existing">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {sortedMalls
                    .filter(m => m.category === 'existing')
                    .slice((previewPage - 1) * ITEMS_PER_PAGE, previewPage * ITEMS_PER_PAGE)
                    .map((mall) => (
                      <MallCard key={mall.id} mall={mall} />
                    ))}
                </div>
              </TabsContent>

              <TabsContent value="upcoming">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {sortedMalls
                    .filter(m => m.category === 'upcoming')
                    .slice((previewPage - 1) * ITEMS_PER_PAGE, previewPage * ITEMS_PER_PAGE)
                    .map((mall) => (
                      <MallCard key={mall.id} mall={mall} />
                    ))}
                </div>
              </TabsContent>
            </Tabs>

            {/* Pagination */}
            {Math.ceil((activePreviewTab === 'existing' ? existingMalls : upcomingMalls) / ITEMS_PER_PAGE) > 1 && (
              <div className="flex justify-center items-center gap-2 mt-8">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setPreviewPage(p => Math.max(1, p - 1))}
                  disabled={previewPage === 1}
                >
                  <ChevronLeft className="w-4 h-4" />
                </Button>
                <span className="text-sm text-slate-600 dark:text-slate-400">
                  Page {previewPage} of {Math.ceil((activePreviewTab === 'existing' ? existingMalls : upcomingMalls) / ITEMS_PER_PAGE)}
                </span>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setPreviewPage(p => p + 1)}
                  disabled={previewPage >= Math.ceil((activePreviewTab === 'existing' ? existingMalls : upcomingMalls) / ITEMS_PER_PAGE)}
                >
                  <ChevronRight className="w-4 h-4" />
                </Button>
              </div>
            )}
          </main>
        </div>
      ) : (
        /* CMS Mode */
        <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Statistics Cards */}
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <Card>
              <CardContent className="p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-slate-500 dark:text-slate-400">Total Malls</p>
                    <p className="text-2xl font-bold text-slate-900 dark:text-white">{totalMalls}</p>
                  </div>
                  <Building2 className="w-8 h-8 text-emerald-500" />
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-slate-500 dark:text-slate-400">Existing Malls</p>
                    <p className="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{existingMalls}</p>
                  </div>
                  <div className="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <Store className="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                  </div>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-slate-500 dark:text-slate-400">Upcoming Malls</p>
                    <p className="text-2xl font-bold text-amber-600 dark:text-amber-400">{upcomingMalls}</p>
                  </div>
                  <div className="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <Sparkles className="w-6 h-6 text-amber-600 dark:text-amber-400" />
                  </div>
                </div>
              </CardContent>
            </Card>
            <Card>
              <CardContent className="p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-slate-500 dark:text-slate-400">Locations</p>
                    <p className="text-2xl font-bold text-slate-900 dark:text-white">{areaNames.length}</p>
                  </div>
                  <MapPin className="w-8 h-8 text-purple-500" />
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Quick Navigation Cards */}
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
            <Link href="/admin/locations">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 flex flex-col items-center text-center">
                  <MapPinned className="w-8 h-8 text-emerald-500 mb-2" />
                  <p className="font-medium text-slate-900 dark:text-white">Locations</p>
                  <p className="text-xs text-slate-500 dark:text-slate-400">States, Cities, Areas</p>
                </CardContent>
              </Card>
            </Link>
            <Link href="/admin/categories">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 flex flex-col items-center text-center">
                  <Tag className="w-8 h-8 text-blue-500 mb-2" />
                  <p className="font-medium text-slate-900 dark:text-white">Categories</p>
                  <p className="text-xs text-slate-500 dark:text-slate-400">Store Categories</p>
                </CardContent>
              </Card>
            </Link>
            <Link href="/admin/features">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 flex flex-col items-center text-center">
                  <Star className="w-8 h-8 text-amber-500 mb-2" />
                  <p className="font-medium text-slate-900 dark:text-white">Features</p>
                  <p className="text-xs text-slate-500 dark:text-slate-400">Mall Features</p>
                </CardContent>
              </Card>
            </Link>
            <Link href="/admin/amenities">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 flex flex-col items-center text-center">
                  <Coffee className="w-8 h-8 text-purple-500 mb-2" />
                  <p className="font-medium text-slate-900 dark:text-white">Amenities</p>
                  <p className="text-xs text-slate-500 dark:text-slate-400">Mall Amenities</p>
                </CardContent>
              </Card>
            </Link>
            <Link href="/admin/ads">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 flex flex-col items-center text-center">
                  <Megaphone className="w-8 h-8 text-red-500 mb-2" />
                  <p className="font-medium text-slate-900 dark:text-white">Ads</p>
                  <p className="text-xs text-slate-500 dark:text-slate-400">Advertisements</p>
                </CardContent>
              </Card>
            </Link>
            <Link href="/admin/settings">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 flex flex-col items-center text-center">
                  <Settings className="w-8 h-8 text-slate-500 mb-2" />
                  <p className="font-medium text-slate-900 dark:text-white">Settings</p>
                  <p className="text-xs text-slate-500 dark:text-slate-400">Site Settings</p>
                </CardContent>
              </Card>
            </Link>
          </div>

          {/* Pages Link */}
          <div className="mb-8">
            <Link href="/admin/pages">
              <Card className="hover:shadow-md transition-shadow cursor-pointer">
                <CardContent className="p-4 flex items-center gap-4">
                  <FileText className="w-8 h-8 text-indigo-500" />
                  <div>
                    <p className="font-medium text-slate-900 dark:text-white">Pages</p>
                    <p className="text-sm text-slate-500 dark:text-slate-400">Manage static pages like About, Contact, Privacy Policy</p>
                  </div>
                </CardContent>
              </Card>
            </Link>
          </div>

          {/* Mall Management Section */}
          <Card>
            <CardHeader>
              <div className="flex flex-col gap-4">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                  <CardTitle className="flex items-center gap-2">
                    <Building2 className="w-5 h-5 text-emerald-500" />
                    Mall Management
                  </CardTitle>
                  <Button onClick={handleAddNew} className="whitespace-nowrap w-full sm:w-auto">
                    <Plus className="w-4 h-4 mr-2" />
                    Add Mall
                  </Button>
                </div>
                {/* Filters Row */}
                <div className="flex flex-wrap items-center gap-2">
                  <div className="relative flex-1 min-w-[200px]">
                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                    <Input
                      placeholder="Search malls..."
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      className="pl-10 w-full"
                    />
                  </div>
                  {/* Status Filter Buttons */}
                  <div className="flex items-center gap-1">
                    <Button
                      variant={statusFilter === 'all' ? 'default' : 'outline'}
                      size="sm"
                      onClick={() => setStatusFilter('all')}
                      className={`h-8 ${statusFilter === 'all' ? 'bg-slate-700 hover:bg-slate-800' : ''}`}
                    >
                      All
                    </Button>
                    <Button
                      variant={statusFilter === 'existing' ? 'default' : 'outline'}
                      size="sm"
                      onClick={() => setStatusFilter('existing')}
                      className={`h-8 ${statusFilter === 'existing' ? 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white border-0' : 'border-emerald-300 text-emerald-600 hover:bg-emerald-50 dark:border-emerald-700 dark:text-emerald-400 dark:hover:bg-emerald-950'}`}
                    >
                      <Store className="w-3.5 h-3.5 mr-1" />
                      Existing
                    </Button>
                    <Button
                      variant={statusFilter === 'upcoming' ? 'default' : 'outline'}
                      size="sm"
                      onClick={() => setStatusFilter('upcoming')}
                      className={`h-8 ${statusFilter === 'upcoming' ? 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white border-0' : 'border-amber-300 text-amber-600 hover:bg-amber-50 dark:border-amber-700 dark:text-amber-400 dark:hover:bg-amber-950'}`}
                    >
                      <Sparkles className="w-3.5 h-3.5 mr-1" />
                      Upcoming
                    </Button>
                  </div>
                  <Select value={mallStateFilter} onValueChange={setMallStateFilter}>
                    <SelectTrigger className="w-full sm:w-36">
                      <SelectValue placeholder="State" />
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
                  <Select 
                    value={mallCityFilter} 
                    onValueChange={setMallCityFilter}
                    disabled={mallStateFilter === 'all'}
                  >
                    <SelectTrigger className="w-full sm:w-36">
                      <SelectValue placeholder="City" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">All Cities</SelectItem>
                      {citiesForMallState.map((city) => (
                        <SelectItem key={city.id} value={city.name}>
                          {city.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <Select 
                    value={mallAreaFilter} 
                    onValueChange={setMallAreaFilter}
                    disabled={mallCityFilter === 'all'}
                  >
                    <SelectTrigger className="w-full sm:w-36">
                      <SelectValue placeholder="Area" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">All Areas</SelectItem>
                      {areasForMallCity.map((area) => (
                        <SelectItem key={area.id} value={area.name}>
                          {area.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  {/* Clear Filters Button */}
                  {(mallStateFilter !== 'all' || mallCityFilter !== 'all' || mallAreaFilter !== 'all' || statusFilter !== 'all') && (
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={() => {
                        setMallStateFilter('all');
                        setMallCityFilter('all');
                        setMallAreaFilter('all');
                        setStatusFilter('all');
                      }}
                      className="text-slate-600 dark:text-slate-400"
                    >
                      <RefreshCw className="w-4 h-4 mr-1" />
                      Clear
                    </Button>
                  )}
                  {/* Results count */}
                  <div className="hidden sm:block text-sm text-slate-500 dark:text-slate-400 ml-auto">
                    {filteredMalls.length} mall{filteredMalls.length !== 1 ? 's' : ''}
                  </div>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              {/* View Mode Toggle for Malls */}
              <div className="flex items-center gap-2 mb-4">
                <span className="text-sm text-slate-500 dark:text-slate-400">View:</span>
                <div className="flex bg-slate-100 dark:bg-slate-800 rounded-lg p-1">
                  <button
                    onClick={() => setMallViewMode('table')}
                    className={`px-3 py-1 rounded-md text-sm transition-colors ${
                      mallViewMode === 'table'
                        ? 'bg-white dark:bg-slate-700 shadow-sm'
                        : ''
                    }`}
                  >
                    Table
                  </button>
                  <button
                    onClick={() => setMallViewMode('grouped')}
                    className={`px-3 py-1 rounded-md text-sm transition-colors ${
                      mallViewMode === 'grouped'
                        ? 'bg-white dark:bg-slate-700 shadow-sm'
                        : ''
                    }`}
                  >
                    Grouped
                  </button>
                </div>
              </div>

              {loading ? (
                <div className="flex items-center justify-center py-12">
                  <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
                </div>
              ) : mallViewMode === 'table' ? (
                <div className="rounded-md border">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead className="w-12">Image</TableHead>
                        <TableHead 
                          className="cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800"
                          onClick={() => handleSort('name')}
                        >
                          <div className="flex items-center gap-1">
                            Name
                            {sortField === 'name' && (
                              sortDirection === 'asc' ? <ArrowUp className="w-4 h-4" /> : <ArrowDown className="w-4 h-4" />
                            )}
                          </div>
                        </TableHead>
                        <TableHead>Address</TableHead>
                        <TableHead 
                          className="cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800"
                          onClick={() => handleSort('area')}
                        >
                          <div className="flex items-center gap-1">
                            Area
                            {sortField === 'area' && (
                              sortDirection === 'asc' ? <ArrowUp className="w-4 h-4" /> : <ArrowDown className="w-4 h-4" />
                            )}
                          </div>
                        </TableHead>
                        <TableHead 
                          className="cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800"
                          onClick={() => handleSort('city')}
                        >
                          <div className="flex items-center gap-1">
                            City
                            {sortField === 'city' && (
                              sortDirection === 'asc' ? <ArrowUp className="w-4 h-4" /> : <ArrowDown className="w-4 h-4" />
                            )}
                          </div>
                        </TableHead>
                        <TableHead 
                          className="cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800"
                          onClick={() => handleSort('state')}
                        >
                          <div className="flex items-center gap-1">
                            State
                            {sortField === 'state' && (
                              sortDirection === 'asc' ? <ArrowUp className="w-4 h-4" /> : <ArrowDown className="w-4 h-4" />
                            )}
                          </div>
                        </TableHead>
                        <TableHead 
                          className="cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800"
                          onClick={() => handleSort('status')}
                        >
                          <div className="flex items-center gap-1">
                            Status
                            {sortField === 'status' && (
                              sortDirection === 'asc' ? <ArrowUp className="w-4 h-4" /> : <ArrowDown className="w-4 h-4" />
                            )}
                          </div>
                        </TableHead>
                        <TableHead className="w-28">Actions</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {sortedMalls.map((mall) => (
                        <TableRow key={mall.id}>
                          <TableCell>
                            {mall.imageUrl ? (
                              <Image
                                src={mall.imageUrl}
                                alt={mall.name}
                                width={40}
                                height={40}
                                className="rounded-md object-cover"
                              />
                            ) : (
                              <div className="w-10 h-10 bg-slate-200 dark:bg-slate-700 rounded-md flex items-center justify-center">
                                <Building2 className="w-5 h-5 text-slate-400" />
                              </div>
                            )}
                          </TableCell>
                          <TableCell className="font-medium">{mall.name}</TableCell>
                          <TableCell className="max-w-xs truncate">{mall.address}</TableCell>
                          <TableCell>{mall.area}</TableCell>
                          <TableCell>{mall.city || '-'}</TableCell>
                          <TableCell>{mall.state || '-'}</TableCell>
                          <TableCell>
                            <Badge className={mall.category === 'existing' 
                              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 hover:bg-emerald-200' 
                              : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 hover:bg-amber-200'
                            }>
                              {mall.category === 'existing' ? (
                                <><Store className="w-3 h-3 mr-1" /> Existing</>
                              ) : (
                                <><Sparkles className="w-3 h-3 mr-1" /> Upcoming</>
                              )}
                            </Badge>
                          </TableCell>
                          <TableCell>
                            <div className="flex items-center gap-1">
                              <Button
                                variant="ghost"
                                size="icon"
                                className="h-8 w-8 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950"
                                onClick={() => window.open(`/mall/${mall.id}`, '_blank')}
                                title="View"
                              >
                                <Eye className="w-4 h-4" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="icon"
                                className="h-8 w-8 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950"
                                onClick={() => handleEdit(mall)}
                                title="Edit"
                              >
                                <Pencil className="w-4 h-4" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="icon"
                                className="h-8 w-8 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950"
                                onClick={() => handleDeleteClick(mall)}
                                title="Delete"
                              >
                                <Trash2 className="w-4 h-4" />
                              </Button>
                            </div>
                          </TableCell>
                        </TableRow>
                      ))}
                      {sortedMalls.length === 0 && (
                        <TableRow>
                          <TableCell colSpan={8} className="text-center py-12 text-slate-500 dark:text-slate-400">
                            No malls found. Click &quot;Add Mall&quot; to create one.
                          </TableCell>
                        </TableRow>
                      )}
                    </TableBody>
                  </Table>
                </div>
              ) : (
                /* Grouped View */
                <div className="space-y-6">
                  {Object.entries(groupedMalls).map(([state, cities]) => (
                    <div key={state} className="border rounded-lg overflow-hidden">
                      <div className="bg-slate-100 dark:bg-slate-800 px-4 py-2 font-medium flex items-center gap-2">
                        <MapPin className="w-4 h-4 text-emerald-500" />
                        {state}
                        <Badge variant="outline" className="ml-2">
                          {Object.values(cities).flat().length} malls
                        </Badge>
                      </div>
                      {Object.entries(cities).map(([city, mallsInCity]) => (
                        <div key={city} className="border-t">
                          <div className="bg-slate-50 dark:bg-slate-900 px-4 py-1.5 text-sm flex items-center gap-2">
                            <Building className="w-3 h-3 text-blue-500" />
                            {city}
                            <Badge variant="outline" className="ml-2 text-xs">
                              {mallsInCity.length}
                            </Badge>
                          </div>
                          <div className="divide-y">
                            {mallsInCity.map((mall) => (
                              <div 
                                key={mall.id} 
                                className="px-4 py-3 flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800/50"
                              >
                                <div className="flex items-center gap-3">
                                  {mall.imageUrl ? (
                                    <Image
                                      src={mall.imageUrl}
                                      alt={mall.name}
                                      width={32}
                                      height={32}
                                      className="rounded object-cover"
                                    />
                                  ) : (
                                    <div className="w-8 h-8 bg-slate-200 dark:bg-slate-700 rounded flex items-center justify-center">
                                      <Building2 className="w-4 h-4 text-slate-400" />
                                    </div>
                                  )}
                                  <div>
                                    <p className="font-medium">{mall.name}</p>
                                    <p className="text-xs text-slate-500 dark:text-slate-400">{mall.area}</p>
                                  </div>
                                  <Badge className={`ml-2 ${mall.category === 'existing' 
                                    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' 
                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                                  }`}>
                                    {mall.category}
                                  </Badge>
                                </div>
                                <div className="flex items-center gap-1">
                                  <Button
                                    variant="ghost"
                                    size="icon"
                                    className="h-7 w-7 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950"
                                    onClick={() => window.open(`/mall/${mall.id}`, '_blank')}
                                    title="View"
                                  >
                                    <Eye className="w-3.5 h-3.5" />
                                  </Button>
                                  <Button
                                    variant="ghost"
                                    size="icon"
                                    className="h-7 w-7 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-emerald-950"
                                    onClick={() => handleEdit(mall)}
                                    title="Edit"
                                  >
                                    <Pencil className="w-3.5 h-3.5" />
                                  </Button>
                                  <Button
                                    variant="ghost"
                                    size="icon"
                                    className="h-7 w-7 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950"
                                    onClick={() => handleDeleteClick(mall)}
                                    title="Delete"
                                  >
                                    <Trash2 className="w-3.5 h-3.5" />
                                  </Button>
                                </div>
                              </div>
                            ))}
                          </div>
                        </div>
                      ))}
                    </div>
                  ))}
                  {Object.keys(groupedMalls).length === 0 && (
                    <div className="text-center py-12 text-slate-500 dark:text-slate-400">
                      No malls found. Click &quot;Add Mall&quot; to create one.
                    </div>
                  )}
                </div>
              )}
            </CardContent>
          </Card>
        </main>
      )}

      {/* Mall Form Dialog */}
      <Dialog open={isFormOpen} onOpenChange={setIsFormOpen}>
        <DialogContent className="max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
          <DialogHeader>
            <DialogTitle>{editingMall ? 'Edit Mall' : 'Add New Mall'}</DialogTitle>
            <DialogDescription>
              {editingMall ? 'Update the mall details below.' : 'Fill in the details for the new mall.'}
            </DialogDescription>
          </DialogHeader>
          
          <Form {...form}>
            <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4 overflow-y-auto flex-1 pr-2">
              <Tabs defaultValue="basic" className="w-full">
                <TabsList className="grid grid-cols-4 mb-4">
                  <TabsTrigger value="basic">Basic Info</TabsTrigger>
                  <TabsTrigger value="location">Location</TabsTrigger>
                  <TabsTrigger value="features">Features</TabsTrigger>
                  <TabsTrigger value="seo">SEO</TabsTrigger>
                </TabsList>

                <TabsContent value="basic" className="space-y-4">
                  <FormField
                    control={form.control}
                    name="name"
                    rules={{ required: 'Name is required' }}
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Name *</FormLabel>
                        <FormControl>
                          <Input placeholder="Mall name" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                  
                  <FormField
                    control={form.control}
                    name="description"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Description</FormLabel>
                        <FormControl>
                          <Textarea placeholder="Mall description" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="address"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Address</FormLabel>
                        <FormControl>
                          <Input placeholder="Full address" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="status"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Status</FormLabel>
                          <Select onValueChange={field.onChange} value={field.value}>
                            <FormControl>
                              <SelectTrigger>
                                <SelectValue placeholder="Select status" />
                              </SelectTrigger>
                            </FormControl>
                            <SelectContent>
                              <SelectItem value="EXISTING">Existing</SelectItem>
                              <SelectItem value="UPCOMING">Upcoming</SelectItem>
                            </SelectContent>
                          </Select>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="openingHours"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Opening Hours</FormLabel>
                          <FormControl>
                            <Input placeholder="e.g., 10:00 AM - 10:00 PM" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  {watchStatus === 'UPCOMING' && (
                    <FormField
                      control={form.control}
                      name="expectedOpeningDate"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Expected Opening Date</FormLabel>
                          <FormControl>
                            <Input type="date" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  )}

                  <FormField
                    control={form.control}
                    name="imageUrl"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Image URL</FormLabel>
                        <FormControl>
                          <Input placeholder="https://example.com/image.jpg" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </TabsContent>

                <TabsContent value="location" className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="state"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>State</FormLabel>
                          <div className="flex gap-2">
                            <Select onValueChange={field.onChange} value={field.value}>
                              <FormControl>
                                <SelectTrigger>
                                  <SelectValue placeholder="Select state" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                {states.map((state) => (
                                  <SelectItem key={state.id} value={state.name}>
                                    {state.name}
                                  </SelectItem>
                                ))}
                              </SelectContent>
                            </Select>
                            <Link href="/admin/locations">
                              <Button type="button" variant="outline" size="icon">
                                <Plus className="w-4 h-4" />
                              </Button>
                            </Link>
                          </div>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="city"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>City</FormLabel>
                          <div className="flex gap-2">
                            <Select onValueChange={field.onChange} value={field.value}>
                              <FormControl>
                                <SelectTrigger>
                                  <SelectValue placeholder="Select city" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                {citiesForState.map((city) => (
                                  <SelectItem key={city.id} value={city.name}>
                                    {city.name}
                                  </SelectItem>
                                ))}
                              </SelectContent>
                            </Select>
                            <Link href="/admin/locations">
                              <Button type="button" variant="outline" size="icon">
                                <Plus className="w-4 h-4" />
                              </Button>
                            </Link>
                          </div>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="area"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Area</FormLabel>
                          <div className="flex gap-2">
                            <Select onValueChange={field.onChange} value={field.value}>
                              <FormControl>
                                <SelectTrigger>
                                  <SelectValue placeholder="Select area" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                {areasForCity.map((area) => (
                                  <SelectItem key={area.id} value={area.name}>
                                    {area.name}
                                  </SelectItem>
                                ))}
                              </SelectContent>
                            </Select>
                            <Link href="/admin/locations">
                              <Button type="button" variant="outline" size="icon">
                                <Plus className="w-4 h-4" />
                              </Button>
                            </Link>
                          </div>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="latitude"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Latitude</FormLabel>
                          <FormControl>
                            <Input 
                              placeholder="e.g., 28.6139" 
                              type="number"
                              step="any"
                              {...field} 
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="longitude"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Longitude</FormLabel>
                          <FormControl>
                            <Input 
                              placeholder="e.g., 77.2090" 
                              type="number"
                              step="any"
                              {...field} 
                            />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  <p className="text-xs text-slate-500 dark:text-slate-400">
                    Click on the map or drag the marker to set coordinates
                  </p>

                  <div className="h-64 rounded-lg overflow-hidden border">
                    <CoordinatePickerMap
                      lat={parseFloat(form.watch('latitude')) || 0}
                      lng={parseFloat(form.watch('longitude')) || 0}
                      name={form.watch('name') || 'Mall Location'}
                      onCoordinatesChange={(lat, lng) => {
                        form.setValue('latitude', lat.toFixed(6));
                        form.setValue('longitude', lng.toFixed(6));
                      }}
                    />
                  </div>
                </TabsContent>

                <TabsContent value="features" className="space-y-4">
                  <FormField
                    control={form.control}
                    name="features"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Features (comma-separated)</FormLabel>
                        <FormControl>
                          <Textarea 
                            placeholder="e.g., Parking, Food Court, Multiplex" 
                            {...field} 
                          />
                        </FormControl>
                        <p className="text-xs text-slate-500 dark:text-slate-400">
                          Available: {features.map(f => f.name).join(', ')}
                        </p>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="amenities"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Amenities (comma-separated)</FormLabel>
                        <FormControl>
                          <Textarea 
                            placeholder="e.g., WiFi, Restrooms, ATMs" 
                            {...field} 
                          />
                        </FormControl>
                        <p className="text-xs text-slate-500 dark:text-slate-400">
                          Available: {amenities.map(a => a.name).join(', ')}
                        </p>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </TabsContent>

                <TabsContent value="seo" className="space-y-4">
                  <FormField
                    control={form.control}
                    name="metaTitle"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Meta Title</FormLabel>
                        <FormControl>
                          <Input placeholder="SEO title for the mall page" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="metaDescription"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Meta Description</FormLabel>
                        <FormControl>
                          <Textarea placeholder="SEO description for the mall page" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="keywords"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Keywords</FormLabel>
                        <FormControl>
                          <Input placeholder="Comma-separated keywords" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="ogImage"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Open Graph Image URL</FormLabel>
                        <FormControl>
                          <Input placeholder="Image for social sharing" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  <FormField
                    control={form.control}
                    name="canonicalUrl"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Canonical URL</FormLabel>
                        <FormControl>
                          <Input placeholder="Canonical URL for the page" {...field} />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />
                </TabsContent>
              </Tabs>

              <DialogFooter className="pt-4 border-t">
                <Button type="button" variant="outline" onClick={() => setIsFormOpen(false)}>
                  Cancel
                </Button>
                <Button type="submit" disabled={isSubmitting}>
                  {isSubmitting && <Loader2 className="w-4 h-4 mr-2 animate-spin" />}
                  {editingMall ? 'Update Mall' : 'Create Mall'}
                </Button>
              </DialogFooter>
            </form>
          </Form>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete Mall</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete &quot;{mallToDelete?.name}&quot;? This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction onClick={handleDeleteConfirm} className="bg-red-600 hover:bg-red-700">
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
}

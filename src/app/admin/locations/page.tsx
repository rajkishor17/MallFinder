'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  ArrowLeft,
  Building2,
  Loader2,
  LogOut,
  User,
  Plus,
  MapPinned,
  ChevronLeft,
  ChevronRight,
  ChevronDown,
  Trash2,
} from 'lucide-react';
import { toast } from 'sonner';

// Types for location hierarchy
interface Area {
  id: string;
  name: string;
}

interface City {
  id: string;
  name: string;
  stateId: string;
  areas?: Area[];
}

interface State {
  id: string;
  name: string;
  cities?: City[];
}

export default function LocationsManagement() {
  const router = useRouter();
  const [states, setStates] = useState<State[]>([]);
  const [loading, setLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [authUser, setAuthUser] = useState<{ email: string; name: string | null } | null>(null);

  // Form states
  const [newStateName, setNewStateName] = useState('');
  const [newCityName, setNewCityName] = useState('');
  const [newAreaName, setNewAreaName] = useState('');
  const [selectedStateId, setSelectedStateId] = useState<string>('');
  const [selectedCityId, setSelectedCityId] = useState<string>('');
  const [isAddingState, setIsAddingState] = useState(false);
  const [isAddingCity, setIsAddingCity] = useState(false);
  const [isAddingArea, setIsAddingArea] = useState(false);
  const [expandedStates, setExpandedStates] = useState<Set<string>>(new Set());
  const [expandedCities, setExpandedCities] = useState<Set<string>>(new Set());

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
          router.push('/admin/login');
        }
      } catch {
        router.push('/admin/login');
      } finally {
        setLoading(false);
      }
    };
    checkAuth();
  }, [router]);

  // Fetch states
  const fetchStates = useCallback(async () => {
    try {
      const response = await fetch('/api/states');
      if (!response.ok) throw new Error('Failed to fetch states');
      const data = await response.json();
      setStates(data);
    } catch {
      toast.error('Failed to load locations');
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      setLoading(true);
      fetchStates().finally(() => setLoading(false));
    }
  }, [isAuthenticated, fetchStates]);

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

  // Add state
  const handleAddState = async () => {
    if (!newStateName.trim()) {
      toast.error('State name is required');
      return;
    }

    setIsAddingState(true);
    try {
      const response = await fetch('/api/states', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: newStateName.trim() }),
        credentials: 'include',
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to add state');
      }

      toast.success('State added successfully');
      setNewStateName('');
      fetchStates();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : 'Failed to add state');
    } finally {
      setIsAddingState(false);
    }
  };

  // Add city
  const handleAddCity = async () => {
    if (!newCityName.trim()) {
      toast.error('City name is required');
      return;
    }

    if (!selectedStateId) {
      toast.error('Please select a state first');
      return;
    }

    setIsAddingCity(true);
    try {
      const response = await fetch('/api/cities', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: newCityName.trim(), stateId: selectedStateId }),
        credentials: 'include',
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to add city');
      }

      toast.success('City added successfully');
      setNewCityName('');
      fetchStates();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : 'Failed to add city');
    } finally {
      setIsAddingCity(false);
    }
  };

  // Add area
  const handleAddArea = async () => {
    if (!newAreaName.trim()) {
      toast.error('Area name is required');
      return;
    }

    if (!selectedCityId) {
      toast.error('Please select a city first');
      return;
    }

    setIsAddingArea(true);
    try {
      const response = await fetch('/api/areas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: newAreaName.trim(), cityId: selectedCityId }),
        credentials: 'include',
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to add area');
      }

      toast.success('Area added successfully');
      setNewAreaName('');
      fetchStates();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : 'Failed to add area');
    } finally {
      setIsAddingArea(false);
    }
  };

  // Toggle expand
  const toggleStateExpand = (stateId: string) => {
    setExpandedStates((prev) => {
      const newSet = new Set(prev);
      if (newSet.has(stateId)) {
        newSet.delete(stateId);
      } else {
        newSet.add(stateId);
      }
      return newSet;
    });
  };

  const toggleCityExpand = (cityId: string) => {
    setExpandedCities((prev) => {
      const newSet = new Set(prev);
      if (newSet.has(cityId)) {
        newSet.delete(cityId);
      } else {
        newSet.add(cityId);
      }
      return newSet;
    });
  };

  // Delete handlers
  const handleDeleteState = async (stateId: string, stateName: string) => {
    if (!confirm(`Are you sure you want to delete "${stateName}"? This will also delete all cities and areas under it.`)) return;
    
    try {
      const response = await fetch(`/api/states/${stateId}`, { method: 'DELETE', credentials: 'include' });
      if (!response.ok) throw new Error('Failed to delete state');
      toast.success('State deleted successfully');
      fetchStates();
    } catch {
      toast.error('Failed to delete state');
    }
  };

  const handleDeleteCity = async (cityId: string, cityName: string) => {
    if (!confirm(`Are you sure you want to delete "${cityName}"? This will also delete all areas under it.`)) return;
    
    try {
      const response = await fetch(`/api/cities/${cityId}`, { method: 'DELETE', credentials: 'include' });
      if (!response.ok) throw new Error('Failed to delete city');
      toast.success('City deleted successfully');
      fetchStates();
    } catch {
      toast.error('Failed to delete city');
    }
  };

  const handleDeleteArea = async (areaId: string, areaName: string) => {
    if (!confirm(`Are you sure you want to delete "${areaName}"?`)) return;
    
    try {
      const response = await fetch(`/api/areas/${areaId}`, { method: 'DELETE', credentials: 'include' });
      if (!response.ok) throw new Error('Failed to delete area');
      toast.success('Area deleted successfully');
      fetchStates();
    } catch {
      toast.error('Failed to delete area');
    }
  };

  // Calculate stats
  const totalCities = states.reduce((acc, s) => acc + (s.cities?.length || 0), 0);
  const totalAreas = states.reduce((acc, s) => {
    return acc + (s.cities?.reduce((a, c) => a + (c.areas?.length || 0), 0) || 0);
  }, 0);

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 flex items-center justify-center">
        <div className="flex flex-col items-center gap-4">
          <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
          <p className="text-muted-foreground">Loading...</p>
        </div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return null;
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900">
      {/* Header */}
      <header className="bg-white dark:bg-slate-900 border-b shadow-sm sticky top-0 z-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <div className="flex items-center gap-3">
              <div className="bg-gradient-to-r from-emerald-500 to-teal-500 p-2 rounded-lg">
                <Building2 className="w-6 h-6 text-white" />
              </div>
              <div>
                <h1 className="text-xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                  MallFinder CMS
                </h1>
                <p className="text-xs text-muted-foreground hidden sm:block">
                  Locations Management
                </p>
              </div>
            </div>

            <div className="flex items-center gap-2">
              <Link href="/admin">
                <Button variant="outline" size="sm">
                  <ArrowLeft className="w-4 h-4 mr-2" />
                  Back to Dashboard
                </Button>
              </Link>

              <div className="hidden sm:flex items-center gap-2 ml-4 pl-4 border-l">
                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                  <User className="w-4 h-4" />
                  <span>{authUser?.name || authUser?.email}</span>
                </div>
                <Button
                  variant="ghost"
                  size="sm"
                  onClick={handleLogout}
                  className="text-destructive hover:text-destructive"
                >
                  <LogOut className="w-4 h-4 mr-1" />
                  Logout
                </Button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {/* Statistics */}
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground">
                Total States
              </CardTitle>
              <MapPinned className="w-5 h-5 text-blue-500" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold">{states.length}</div>
              <p className="text-xs text-muted-foreground mt-1">
                States/Regions
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground">
                Total Cities
              </CardTitle>
              <Building2 className="w-5 h-5 text-purple-500" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold">{totalCities}</div>
              <p className="text-xs text-muted-foreground mt-1">
                Cities across all states
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground">
                Total Areas
              </CardTitle>
              <MapPinned className="w-5 h-5 text-emerald-500" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold">{totalAreas}</div>
              <p className="text-xs text-muted-foreground mt-1">
                Areas/Districts
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Add State Section */}
        <Card className="mb-6">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Plus className="w-5 h-5" />
              Add New State
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex gap-2">
              <Input
                placeholder="Enter state name (e.g., Maharashtra, Gujarat)"
                value={newStateName}
                onChange={(e) => setNewStateName(e.target.value)}
                onKeyDown={(e) => {
                  if (e.key === 'Enter') {
                    e.preventDefault();
                    handleAddState();
                  }
                }}
                className="flex-1"
              />
              <Button
                onClick={handleAddState}
                disabled={isAddingState || !newStateName.trim()}
                className="bg-emerald-500 hover:bg-emerald-600"
              >
                {isAddingState ? (
                  <Loader2 className="w-4 h-4 animate-spin" />
                ) : (
                  <Plus className="w-4 h-4" />
                )}
                <span className="ml-2 hidden sm:inline">Add State</span>
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Location Hierarchy */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <MapPinned className="w-5 h-5" />
              Manage Locations (State → City → Area)
            </CardTitle>
          </CardHeader>
          <CardContent className="p-0">
            <ScrollArea className="h-[60vh]">
              <div className="p-4 space-y-2">
                {states.length === 0 ? (
                  <div className="text-center py-12 text-muted-foreground">
                    <MapPinned className="w-12 h-12 mx-auto mb-4 opacity-50" />
                    <p className="text-lg font-medium">No states added yet</p>
                    <p className="text-sm mt-1">Add a state above to get started</p>
                  </div>
                ) : (
                  states.map((state) => (
                    <div key={state.id} className="border rounded-lg overflow-hidden">
                      {/* State Header */}
                      <div className="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-950">
                        <div
                          className="flex items-center gap-2 cursor-pointer flex-1"
                          onClick={() => toggleStateExpand(state.id)}
                        >
                          {expandedStates.has(state.id) ? (
                            <ChevronDown className="w-4 h-4 text-blue-600" />
                          ) : (
                            <ChevronRight className="w-4 h-4 text-blue-600" />
                          )}
                          <Badge className="bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900 dark:text-blue-300">
                            {state.name}
                          </Badge>
                          <span className="text-xs text-muted-foreground">
                            ({state.cities?.length || 0} cities)
                          </span>
                        </div>
                        <Button
                          variant="ghost"
                          size="icon"
                          className="h-7 w-7 text-destructive hover:text-destructive hover:bg-destructive/10"
                          onClick={() => handleDeleteState(state.id, state.name)}
                        >
                          <Trash2 className="w-4 h-4" />
                        </Button>
                      </div>

                      {/* Cities under State */}
                      {expandedStates.has(state.id) && (
                        <div className="bg-slate-50 dark:bg-slate-900 border-t">
                          {/* Add City Form */}
                          <div className="flex gap-2 p-3 border-b bg-white dark:bg-slate-800">
                            <Input
                              placeholder="Add city..."
                              value={selectedStateId === state.id ? newCityName : ''}
                              onChange={(e) => {
                                setSelectedStateId(state.id);
                                setNewCityName(e.target.value);
                              }}
                              onFocus={() => setSelectedStateId(state.id)}
                              onKeyDown={(e) => {
                                if (e.key === 'Enter') {
                                  e.preventDefault();
                                  handleAddCity();
                                }
                              }}
                              className="flex-1 h-9"
                            />
                            <Button
                              size="sm"
                              onClick={handleAddCity}
                              disabled={isAddingCity || selectedStateId !== state.id || !newCityName.trim()}
                              className="bg-purple-500 hover:bg-purple-600"
                            >
                              {isAddingCity && selectedStateId === state.id ? (
                                <Loader2 className="w-4 h-4 animate-spin" />
                              ) : (
                                <Plus className="w-4 h-4" />
                              )}
                            </Button>
                          </div>

                          {/* City List */}
                          <div className="p-2 space-y-1">
                            {(state.cities || []).length === 0 ? (
                              <p className="text-xs text-muted-foreground text-center py-4">No cities added yet</p>
                            ) : (
                              (state.cities || []).map((city) => (
                                <div key={city.id} className="border rounded overflow-hidden bg-white dark:bg-slate-800">
                                  {/* City Header */}
                                  <div className="flex items-center justify-between p-2 hover:bg-purple-50 dark:hover:bg-purple-950">
                                    <div
                                      className="flex items-center gap-2 cursor-pointer flex-1"
                                      onClick={() => toggleCityExpand(city.id)}
                                    >
                                      {expandedCities.has(city.id) ? (
                                        <ChevronDown className="w-4 h-4 text-purple-600" />
                                      ) : (
                                        <ChevronRight className="w-4 h-4 text-purple-600" />
                                      )}
                                      <Badge variant="outline" className="bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950 dark:text-purple-300">
                                        {city.name}
                                      </Badge>
                                      <span className="text-xs text-muted-foreground">
                                        ({city.areas?.length || 0} areas)
                                      </span>
                                    </div>
                                    <Button
                                      variant="ghost"
                                      size="icon"
                                      className="h-6 w-6 text-destructive hover:text-destructive hover:bg-destructive/10"
                                      onClick={() => handleDeleteCity(city.id, city.name)}
                                    >
                                      <Trash2 className="w-3 h-3" />
                                    </Button>
                                  </div>

                                  {/* Areas under City */}
                                  {expandedCities.has(city.id) && (
                                    <div className="bg-slate-100 dark:bg-slate-900 border-t p-2">
                                      {/* Add Area Form */}
                                      <div className="flex gap-2 mb-2">
                                        <Input
                                          placeholder="Add area..."
                                          value={selectedCityId === city.id ? newAreaName : ''}
                                          onChange={(e) => {
                                            setSelectedCityId(city.id);
                                            setNewAreaName(e.target.value);
                                          }}
                                          onFocus={() => setSelectedCityId(city.id)}
                                          onKeyDown={(e) => {
                                            if (e.key === 'Enter') {
                                              e.preventDefault();
                                              handleAddArea();
                                            }
                                          }}
                                          className="flex-1 h-8"
                                        />
                                        <Button
                                          size="sm"
                                          onClick={handleAddArea}
                                          disabled={isAddingArea || selectedCityId !== city.id || !newAreaName.trim()}
                                          className="bg-emerald-500 hover:bg-emerald-600 h-8"
                                        >
                                          {isAddingArea && selectedCityId === city.id ? (
                                            <Loader2 className="w-3 h-3 animate-spin" />
                                          ) : (
                                            <Plus className="w-3 h-3" />
                                          )}
                                        </Button>
                                      </div>

                                      {/* Area List */}
                                      <div className="space-y-1">
                                        {(city.areas || []).length === 0 ? (
                                          <p className="text-xs text-muted-foreground text-center py-2">No areas added</p>
                                        ) : (
                                          (city.areas || []).map((area) => (
                                            <div
                                              key={area.id}
                                              className="flex items-center justify-between p-2 rounded bg-gray-50 dark:bg-slate-800"
                                            >
                                              <Badge variant="secondary">{area.name}</Badge>
                                              <Button
                                                variant="ghost"
                                                size="icon"
                                                className="h-6 w-6 text-destructive hover:text-destructive hover:bg-destructive/10"
                                                onClick={() => handleDeleteArea(area.id, area.name)}
                                              >
                                                <Trash2 className="w-3 h-3" />
                                              </Button>
                                            </div>
                                          ))
                                        )}
                                      </div>
                                    </div>
                                  )}
                                </div>
                              ))
                            )}
                          </div>
                        </div>
                      )}
                    </div>
                  ))
                )}
              </div>
            </ScrollArea>
          </CardContent>
        </Card>
      </main>
    </div>
  );
}

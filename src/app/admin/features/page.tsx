'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
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
  ArrowLeft,
  Building2,
  Loader2,
  Pencil,
  LogOut,
  User,
  Plus,
  Trash2,
  Star,
  Check,
  X,
} from 'lucide-react';
import { toast } from 'sonner';

interface Feature {
  id: string;
  name: string;
  icon?: string | null;
  createdAt: string;
  updatedAt: string;
}

export default function FeaturesManagement() {
  const router = useRouter();
  const [features, setFeatures] = useState<Feature[]>([]);
  const [loading, setLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [authUser, setAuthUser] = useState<{ email: string; name: string | null } | null>(null);

  // Form states
  const [newFeatureName, setNewFeatureName] = useState('');
  const [isAdding, setIsAdding] = useState(false);
  const [editingFeature, setEditingFeature] = useState<Feature | null>(null);
  const [editingName, setEditingName] = useState('');
  const [isUpdating, setIsUpdating] = useState(false);
  const [featureToDelete, setFeatureToDelete] = useState<Feature | null>(null);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);

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

  // Fetch features
  const fetchFeatures = useCallback(async () => {
    try {
      const response = await fetch('/api/features');
      if (!response.ok) throw new Error('Failed to fetch features');
      const data = await response.json();
      setFeatures(data);
    } catch {
      toast.error('Failed to load features');
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      setLoading(true);
      fetchFeatures().finally(() => setLoading(false));
    }
  }, [isAuthenticated, fetchFeatures]);

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

  // Add feature
  const handleAddFeature = async () => {
    if (!newFeatureName.trim()) {
      toast.error('Feature name is required');
      return;
    }

    setIsAdding(true);
    try {
      const response = await fetch('/api/features', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: newFeatureName.trim() }),
        credentials: 'include',
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to add feature');
      }

      toast.success('Feature added successfully');
      setNewFeatureName('');
      fetchFeatures();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : 'Failed to add feature');
    } finally {
      setIsAdding(false);
    }
  };

  // Start editing
  const handleStartEdit = (feature: Feature) => {
    setEditingFeature(feature);
    setEditingName(feature.name);
  };

  // Cancel editing
  const handleCancelEdit = () => {
    setEditingFeature(null);
    setEditingName('');
  };

  // Save edit
  const handleSaveEdit = async () => {
    if (!editingFeature || !editingName.trim()) return;

    setIsUpdating(true);
    try {
      const response = await fetch(`/api/features/${editingFeature.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: editingName.trim() }),
        credentials: 'include',
      });

      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to update feature');
      }

      toast.success('Feature updated successfully');
      handleCancelEdit();
      fetchFeatures();
    } catch (error) {
      toast.error(error instanceof Error ? error.message : 'Failed to update feature');
    } finally {
      setIsUpdating(false);
    }
  };

  // Delete feature
  const handleDeleteClick = (feature: Feature) => {
    setFeatureToDelete(feature);
    setIsDeleteDialogOpen(true);
  };

  const handleDeleteConfirm = async () => {
    if (!featureToDelete) return;

    try {
      const response = await fetch(`/api/features/${featureToDelete.id}`, {
        method: 'DELETE',
        credentials: 'include',
      });

      if (!response.ok) throw new Error('Failed to delete feature');

      toast.success('Feature deleted successfully');
      fetchFeatures();
    } catch {
      toast.error('Failed to delete feature');
    } finally {
      setIsDeleteDialogOpen(false);
      setFeatureToDelete(null);
    }
  };

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
                  Features Management
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
                Total Features
              </CardTitle>
              <Star className="w-5 h-5 text-amber-500" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold">{features.length}</div>
              <p className="text-xs text-muted-foreground mt-1">
                Mall features
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Add Feature Section */}
        <Card className="mb-6">
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Plus className="w-5 h-5" />
              Add New Feature
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="flex gap-2">
              <Input
                placeholder="Enter feature name (e.g., Luxury Brands, Food Court, Cinema)"
                value={newFeatureName}
                onChange={(e) => setNewFeatureName(e.target.value)}
                onKeyDown={(e) => {
                  if (e.key === 'Enter') {
                    e.preventDefault();
                    handleAddFeature();
                  }
                }}
                className="flex-1"
              />
              <Button
                onClick={handleAddFeature}
                disabled={isAdding || !newFeatureName.trim()}
                className="bg-emerald-500 hover:bg-emerald-600"
              >
                {isAdding ? (
                  <Loader2 className="w-4 h-4 animate-spin" />
                ) : (
                  <Plus className="w-4 h-4" />
                )}
                <span className="ml-2 hidden sm:inline">Add Feature</span>
              </Button>
            </div>
          </CardContent>
        </Card>

        {/* Features Table */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Star className="w-5 h-5" />
              Manage Features
            </CardTitle>
          </CardHeader>
          <CardContent className="p-0">
            {loading ? (
              <div className="flex items-center justify-center py-12">
                <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
              </div>
            ) : features.length === 0 ? (
              <div className="text-center py-12">
                <Star className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                <h3 className="text-lg font-semibold mb-2">No features found</h3>
                <p className="text-muted-foreground mb-4">
                  Add your first feature above.
                </p>
              </div>
            ) : (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead className="w-20">ID</TableHead>
                    <TableHead>Feature Name</TableHead>
                    <TableHead>Created</TableHead>
                    <TableHead className="w-28">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {features.map((feature) => (
                    <TableRow key={feature.id} className="hover:bg-muted/50">
                      <TableCell className="font-mono text-xs text-muted-foreground">
                        ...{feature.id.slice(-6)}
                      </TableCell>
                      <TableCell>
                        {editingFeature?.id === feature.id ? (
                          <div className="flex items-center gap-2">
                            <Input
                              value={editingName}
                              onChange={(e) => setEditingName(e.target.value)}
                              onKeyDown={(e) => {
                                if (e.key === 'Enter') handleSaveEdit();
                                if (e.key === 'Escape') handleCancelEdit();
                              }}
                              className="h-8"
                              autoFocus
                            />
                            <Button
                              size="icon"
                              variant="ghost"
                              onClick={handleSaveEdit}
                              disabled={isUpdating}
                              className="h-8 w-8 text-emerald-500"
                            >
                              {isUpdating ? <Loader2 className="w-4 h-4 animate-spin" /> : <Check className="w-4 h-4" />}
                            </Button>
                            <Button
                              size="icon"
                              variant="ghost"
                              onClick={handleCancelEdit}
                              className="h-8 w-8 text-muted-foreground"
                            >
                              <X className="w-4 h-4" />
                            </Button>
                          </div>
                        ) : (
                          <Badge variant="secondary" className="bg-amber-50 text-amber-700 border-amber-200">
                            <Star className="w-3 h-3 mr-1" />
                            {feature.name}
                          </Badge>
                        )}
                      </TableCell>
                      <TableCell>
                        <p className="text-sm text-muted-foreground">
                          {new Date(feature.createdAt).toLocaleDateString()}
                        </p>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1">
                          <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => handleStartEdit(feature)}
                            title="Edit feature"
                            disabled={editingFeature?.id === feature.id}
                          >
                            <Pencil className="w-4 h-4" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => handleDeleteClick(feature)}
                            className="text-destructive hover:text-destructive hover:bg-destructive/10"
                            title="Delete feature"
                          >
                            <Trash2 className="w-4 h-4" />
                          </Button>
                        </div>
                      </TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            )}
          </CardContent>
        </Card>
      </main>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete Feature</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete <strong>{featureToDelete?.name}</strong>?
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
    </div>
  );
}

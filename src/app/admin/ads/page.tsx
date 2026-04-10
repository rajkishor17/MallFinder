'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
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
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogFooter,
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
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
  ArrowLeft,
  Building2,
  Loader2,
  LogOut,
  User,
  Plus,
  Trash2,
  Megaphone,
  Pencil,
  Upload,
  ExternalLink,
  Power,
  Eye,
  EyeOff,
} from 'lucide-react';
import { toast } from 'sonner';
import type { Advertisement, AdPosition, AdType } from '@/types/advertisement';
import { AD_POSITIONS, AD_TYPES } from '@/types/advertisement';
import type { Mall } from '@/types/mall';

export default function AdsManagement() {
  const router = useRouter();
  const [ads, setAds] = useState<Advertisement[]>([]);
  const [malls, setMalls] = useState<Mall[]>([]);
  const [loading, setLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [authUser, setAuthUser] = useState<{ email: string; name: string | null } | null>(null);

  // Form states
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [editingAd, setEditingAd] = useState<Advertisement | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isUploadingImage, setIsUploadingImage] = useState(false);
  const [adToDelete, setAdToDelete] = useState<Advertisement | null>(null);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);

  const [adForm, setAdForm] = useState({
    title: '',
    position: 'HEADER' as AdPosition,
    type: 'IMAGE' as AdType,
    imageUrl: '',
    linkUrl: '',
    htmlCode: '',
    isActive: true,
    sortOrder: 0,
    mallId: '',
  });

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

  // Fetch ads
  const fetchAds = useCallback(async () => {
    try {
      const response = await fetch('/api/advertisements');
      if (!response.ok) throw new Error('Failed to fetch ads');
      const data = await response.json();
      setAds(data);
    } catch {
      toast.error('Failed to load advertisements');
    }
  }, []);

  // Fetch malls for dropdown
  const fetchMalls = useCallback(async () => {
    try {
      const response = await fetch('/api/malls');
      if (!response.ok) throw new Error('Failed to fetch malls');
      const data = await response.json();
      setMalls(data);
    } catch {
      console.error('Failed to load malls');
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      setLoading(true);
      Promise.all([fetchAds(), fetchMalls()]).finally(() => setLoading(false));
    }
  }, [isAuthenticated, fetchAds, fetchMalls]);

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

  // Open form for new ad
  const handleAddNew = () => {
    setEditingAd(null);
    setAdForm({
      title: '',
      position: 'HEADER',
      type: 'IMAGE',
      imageUrl: '',
      linkUrl: '',
      htmlCode: '',
      isActive: true,
      sortOrder: 0,
      mallId: '',
    });
    setIsFormOpen(true);
  };

  // Open edit form
  const handleEdit = (ad: Advertisement) => {
    setEditingAd(ad);
    setAdForm({
      title: ad.title,
      position: ad.position,
      type: ad.type,
      imageUrl: ad.imageUrl || '',
      linkUrl: ad.linkUrl || '',
      htmlCode: ad.htmlCode || '',
      isActive: ad.isActive,
      sortOrder: ad.sortOrder,
      mallId: ad.mallId || '',
    });
    setIsFormOpen(true);
  };

  // Handle image upload
  const handleImageUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
      toast.error('Please select an image file');
      return;
    }

    if (file.size > 5 * 1024 * 1024) {
      toast.error('Image size should be less than 5MB');
      return;
    }

    setIsUploadingImage(true);
    const reader = new FileReader();
    reader.onload = async () => {
      try {
        const base64 = reader.result as string;
        const uploadResponse = await fetch('/api/upload', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ image: base64, type: 'ads' }),
        });

        if (!uploadResponse.ok) throw new Error('Failed to upload image');
        const { url } = await uploadResponse.json();
        setAdForm({ ...adForm, imageUrl: url });
        toast.success('Image uploaded successfully');
      } catch {
        toast.error('Failed to upload image');
      } finally {
        setIsUploadingImage(false);
      }
    };
    reader.readAsDataURL(file);
    e.target.value = '';
  };

  // Submit form
  const handleSubmit = async () => {
    if (!adForm.title) {
      toast.error('Title is required');
      return;
    }

    if (adForm.type === 'IMAGE' && !adForm.imageUrl) {
      toast.error('Image URL is required for image type ads');
      return;
    }

    if (adForm.type === 'HTML' && !adForm.htmlCode) {
      toast.error('HTML code is required for HTML type ads');
      return;
    }

    const mallSpecificPositions = ['MALL_HEADER', 'MALL_FOOTER', 'MALL_SIDEBAR'];
    if (mallSpecificPositions.includes(adForm.position) && !adForm.mallId) {
      toast.error('Please select a mall for mall-specific ad positions');
      return;
    }

    setIsSubmitting(true);
    try {
      const url = editingAd ? `/api/advertisements/${editingAd.id}` : '/api/advertisements';
      const method = editingAd ? 'PUT' : 'POST';

      const response = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(adForm),
        credentials: 'include',
      });

      if (!response.ok) throw new Error('Failed to save advertisement');

      toast.success(editingAd ? 'Advertisement updated successfully' : 'Advertisement created successfully');
      setIsFormOpen(false);
      fetchAds();
    } catch {
      toast.error('Failed to save advertisement');
    } finally {
      setIsSubmitting(false);
    }
  };

  // Toggle ad status
  const handleToggleStatus = async (ad: Advertisement) => {
    try {
      const response = await fetch(`/api/advertisements/${ad.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ isActive: !ad.isActive }),
        credentials: 'include',
      });

      if (!response.ok) throw new Error('Failed to update advertisement');

      toast.success(ad.isActive ? 'Advertisement deactivated' : 'Advertisement activated');
      fetchAds();
    } catch {
      toast.error('Failed to update advertisement');
    }
  };

  // Delete ad
  const handleDeleteClick = (ad: Advertisement) => {
    setAdToDelete(ad);
    setIsDeleteDialogOpen(true);
  };

  const handleDeleteConfirm = async () => {
    if (!adToDelete) return;

    try {
      const response = await fetch(`/api/advertisements/${adToDelete.id}`, {
        method: 'DELETE',
        credentials: 'include',
      });

      if (!response.ok) throw new Error('Failed to delete advertisement');

      toast.success('Advertisement deleted successfully');
      fetchAds();
    } catch {
      toast.error('Failed to delete advertisement');
    } finally {
      setIsDeleteDialogOpen(false);
      setAdToDelete(null);
    }
  };

  // Stats
  const activeAds = ads.filter(a => a.isActive).length;
  const inactiveAds = ads.filter(a => !a.isActive).length;

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
                  Advertisements Management
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
                Total Ads
              </CardTitle>
              <Megaphone className="w-5 h-5 text-blue-500" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold">{ads.length}</div>
              <p className="text-xs text-muted-foreground mt-1">
                All advertisements
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground">
                Active Ads
              </CardTitle>
              <Eye className="w-5 h-5 text-emerald-500" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold text-emerald-600">{activeAds}</div>
              <p className="text-xs text-muted-foreground mt-1">
                Currently showing
              </p>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium text-muted-foreground">
                Inactive Ads
              </CardTitle>
              <EyeOff className="w-5 h-5 text-gray-500" />
            </CardHeader>
            <CardContent>
              <div className="text-3xl font-bold text-gray-600">{inactiveAds}</div>
              <p className="text-xs text-muted-foreground mt-1">
                Paused
              </p>
            </CardContent>
          </Card>
        </div>

        {/* Actions Bar */}
        <div className="flex justify-end mb-4">
          <Button onClick={handleAddNew} className="bg-emerald-500 hover:bg-emerald-600">
            <Plus className="w-4 h-4 mr-2" />
            Add New Advertisement
          </Button>
        </div>

        {/* Ads Table */}
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Megaphone className="w-5 h-5" />
              Manage Advertisements
            </CardTitle>
          </CardHeader>
          <CardContent className="p-0">
            {loading ? (
              <div className="flex items-center justify-center py-12">
                <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
              </div>
            ) : ads.length === 0 ? (
              <div className="text-center py-12">
                <Megaphone className="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                <h3 className="text-lg font-semibold mb-2">No advertisements found</h3>
                <p className="text-muted-foreground mb-4">
                  Create your first advertisement above.
                </p>
              </div>
            ) : (
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Title</TableHead>
                    <TableHead>Position</TableHead>
                    <TableHead>Type</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead className="w-32">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {ads.map((ad) => (
                    <TableRow key={ad.id} className="hover:bg-muted/50">
                      <TableCell>
                        <div>
                          <p className="font-medium">{ad.title}</p>
                          {ad.mallId && (
                            <p className="text-xs text-muted-foreground">
                              Mall: {malls.find(m => m.id === ad.mallId)?.name || 'Unknown'}
                            </p>
                          )}
                        </div>
                      </TableCell>
                      <TableCell>
                        <Badge variant="outline">{ad.position}</Badge>
                      </TableCell>
                      <TableCell>
                        <Badge variant="secondary">{ad.type}</Badge>
                      </TableCell>
                      <TableCell>
                        <Badge className={ad.isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700'}>
                          {ad.isActive ? 'Active' : 'Inactive'}
                        </Badge>
                      </TableCell>
                      <TableCell>
                        <div className="flex items-center gap-1">
                          <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => handleToggleStatus(ad)}
                            title={ad.isActive ? 'Deactivate' : 'Activate'}
                          >
                            <Power className={`w-4 h-4 ${ad.isActive ? 'text-emerald-500' : 'text-gray-400'}`} />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => handleEdit(ad)}
                            title="Edit advertisement"
                          >
                            <Pencil className="w-4 h-4" />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon"
                            onClick={() => handleDeleteClick(ad)}
                            className="text-destructive hover:text-destructive hover:bg-destructive/10"
                            title="Delete advertisement"
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

      {/* Add/Edit Ad Dialog */}
      <Dialog open={isFormOpen} onOpenChange={setIsFormOpen}>
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
          <DialogHeader>
            <DialogTitle>
              {editingAd ? 'Edit Advertisement' : 'Create New Advertisement'}
            </DialogTitle>
            <DialogDescription>
              {editingAd
                ? 'Update the advertisement details below.'
                : 'Fill in the details to create a new advertisement.'}
            </DialogDescription>
          </DialogHeader>

          <ScrollArea className="flex-1 pr-4 -mr-4">
            <div className="grid gap-4 py-4 pr-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium mb-2 block">Title *</label>
                  <Input
                    value={adForm.title}
                    onChange={(e) => setAdForm({ ...adForm, title: e.target.value })}
                    placeholder="Ad title"
                  />
                </div>

                <div>
                  <label className="text-sm font-medium mb-2 block">Position</label>
                  <Select value={adForm.position} onValueChange={(value) => setAdForm({ ...adForm, position: value as AdPosition })}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {AD_POSITIONS.map((pos) => (
                        <SelectItem key={pos} value={pos}>{pos}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="text-sm font-medium mb-2 block">Type</label>
                  <Select value={adForm.type} onValueChange={(value) => setAdForm({ ...adForm, type: value as AdType })}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      {AD_TYPES.map((type) => (
                        <SelectItem key={type} value={type}>{type}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <label className="text-sm font-medium mb-2 block">Sort Order</label>
                  <Input
                    type="number"
                    value={adForm.sortOrder}
                    onChange={(e) => setAdForm({ ...adForm, sortOrder: parseInt(e.target.value) || 0 })}
                  />
                </div>
              </div>

              {['MALL_HEADER', 'MALL_FOOTER', 'MALL_SIDEBAR'].includes(adForm.position) && (
                <div>
                  <label className="text-sm font-medium mb-2 block">Mall (Required for mall-specific positions)</label>
                  <Select value={adForm.mallId} onValueChange={(value) => setAdForm({ ...adForm, mallId: value })}>
                    <SelectTrigger>
                      <SelectValue placeholder="Select a mall" />
                    </SelectTrigger>
                    <SelectContent>
                      {malls.map((mall) => (
                        <SelectItem key={mall.id} value={mall.id}>{mall.name}</SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              )}

              {adForm.type === 'IMAGE' && (
                <>
                  <div>
                    <label className="text-sm font-medium mb-2 block">Image</label>
                    <div className="flex gap-2">
                      <Input
                        value={adForm.imageUrl}
                        onChange={(e) => setAdForm({ ...adForm, imageUrl: e.target.value })}
                        placeholder="Image URL"
                        className="flex-1"
                      />
                      <label className="cursor-pointer">
                        <input
                          type="file"
                          accept="image/*"
                          onChange={handleImageUpload}
                          className="hidden"
                        />
                        <Button variant="outline" type="button" disabled={isUploadingImage}>
                          {isUploadingImage ? <Loader2 className="w-4 h-4 animate-spin" /> : <Upload className="w-4 h-4" />}
                        </Button>
                      </label>
                    </div>
                  </div>

                  {adForm.imageUrl && (
                    <div className="relative w-full h-32 rounded-lg overflow-hidden border">
                      <img src={adForm.imageUrl} alt="Preview" className="w-full h-full object-cover" />
                    </div>
                  )}
                </>
              )}

              {adForm.type === 'HTML' && (
                <div>
                  <label className="text-sm font-medium mb-2 block">HTML Code</label>
                  <Textarea
                    value={adForm.htmlCode}
                    onChange={(e) => setAdForm({ ...adForm, htmlCode: e.target.value })}
                    placeholder="<div>Your HTML code here</div>"
                    className="min-h-[100px] font-mono"
                  />
                </div>
              )}

              <div>
                <label className="text-sm font-medium mb-2 block">Link URL (optional)</label>
                <Input
                  value={adForm.linkUrl}
                  onChange={(e) => setAdForm({ ...adForm, linkUrl: e.target.value })}
                  placeholder="https://example.com"
                />
              </div>

              <div className="flex items-center gap-2">
                <input
                  type="checkbox"
                  id="isActive"
                  checked={adForm.isActive}
                  onChange={(e) => setAdForm({ ...adForm, isActive: e.target.checked })}
                  className="h-4 w-4"
                />
                <label htmlFor="isActive" className="text-sm">Active</label>
              </div>
            </div>
          </ScrollArea>

          <DialogFooter className="border-t pt-4">
            <Button variant="outline" onClick={() => setIsFormOpen(false)}>
              Cancel
            </Button>
            <Button onClick={handleSubmit} disabled={isSubmitting} className="bg-emerald-500 hover:bg-emerald-600">
              {isSubmitting && <Loader2 className="w-4 h-4 mr-2 animate-spin" />}
              {editingAd ? 'Update' : 'Create'}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete Advertisement</AlertDialogTitle>
            <AlertDialogDescription>
              Are you sure you want to delete <strong>{adToDelete?.title}</strong>?
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

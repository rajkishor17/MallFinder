'use client';

import { useEffect, useState, useRef } from 'react';
import { useForm } from 'react-hook-form';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Loader2, Plus, Tag, Upload, X, Image as ImageIcon, GripVertical } from 'lucide-react';
import { toast } from 'sonner';
import type { Store, StoreImage } from '@/types/store';
import { DEFAULT_STORE_CATEGORIES, MAX_STORE_IMAGES } from '@/types/store';
import type { StoreCategory } from '@/types/category';

interface StoreFormData {
  name: string;
  description: string;
  category: string;
  floor: string;
  unitNumber: string;
  phone: string;
  website: string;
  imageUrl: string;
  status: 'OPEN' | 'COMING_SOON' | 'CLOSED';
  openingHours: string;
  weekendHours: string;
  sundayHours: string;
}

interface StoreFormProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSubmit: (data: StoreFormData, images: { url: string; sortOrder: number }[]) => Promise<void>;
  store?: Store | null;
  isSubmitting: boolean;
  categories?: StoreCategory[];
  onCategoryAdded?: () => void;
  isUpcoming?: boolean;
}

const defaultFormData: StoreFormData = {
  name: '',
  description: '',
  category: 'Other',
  floor: '',
  unitNumber: '',
  phone: '',
  website: '',
  imageUrl: '',
  status: 'OPEN',
  openingHours: '',
  weekendHours: '',
  sundayHours: '',
};

interface ImageItem {
  id?: string;
  url: string;
  sortOrder: number;
  isNew?: boolean;
  toDelete?: boolean;
}

export function StoreForm({
  open,
  onOpenChange,
  onSubmit,
  store,
  isSubmitting,
  categories = [],
  onCategoryAdded,
  isUpcoming = false,
}: StoreFormProps) {
  const form = useForm<StoreFormData>({
    defaultValues: defaultFormData,
  });
  
  const [isCategoryDialogOpen, setIsCategoryDialogOpen] = useState(false);
  const [newCategoryName, setNewCategoryName] = useState('');
  const [isAddingCategory, setIsAddingCategory] = useState(false);
  const [images, setImages] = useState<ImageItem[]>([]);
  const [uploadingIndex, setUploadingIndex] = useState<number | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Use database categories if available, otherwise use defaults
  const categoryOptions = categories.length > 0 
    ? categories.map(c => c.name) 
    : DEFAULT_STORE_CATEGORIES;

  // Parse opening hours string to extract weekday, Saturday, and Sunday hours
  const parseOpeningHours = (hoursString: string) => {
    if (!hoursString) return { weekday: '', saturday: '', sunday: '' };
    
    // Try to parse format like "Mon-Fri: 10:00 AM - 9:00 PM, Sat: 10:00 AM - 8:00 PM, Sun: 11:00 AM - 6:00 PM"
    const parts = hoursString.split(',').map(p => p.trim());
    
    let weekday = '';
    let saturday = '';
    let sunday = '';
    
    for (const part of parts) {
      const match = part.match(/(.+?):\s*(.+)/);
      if (match) {
        const [, days, time] = match;
        const daysLower = days.toLowerCase().trim();
        
        if (daysLower.includes('sun') || daysLower === 'sunday') {
          sunday = time.trim();
        } else if (daysLower.includes('sat') || daysLower === 'saturday') {
          saturday = time.trim();
        } else {
          weekday = time.trim();
        }
      }
    }
    
    // If no separate weekend hours found, use weekday hours
    return { weekday, saturday, sunday };
  };

  useEffect(() => {
    if (store) {
      const parsedHours = parseOpeningHours(store.openingHours || '');
      
      form.reset({
        name: store.name,
        description: store.description || '',
        category: store.category,
        floor: store.floor || '',
        unitNumber: store.unitNumber || '',
        phone: store.phone || '',
        website: store.website || '',
        imageUrl: store.imageUrl || '',
        status: store.status,
        openingHours: parsedHours.weekday,
        weekendHours: parsedHours.saturday,
        sundayHours: parsedHours.sunday,
      });
      
      // Load existing images
      const existingImages: ImageItem[] = [];
      if (store.imageUrl) {
        existingImages.push({
          url: store.imageUrl,
          sortOrder: 0,
        });
      }
      if (store.images && store.images.length > 0) {
        store.images.forEach((img) => {
          if (!existingImages.find(i => i.url === img.url)) {
            existingImages.push({
              id: img.id,
              url: img.url,
              sortOrder: img.sortOrder,
            });
          }
        });
      }
      setImages(existingImages.sort((a, b) => a.sortOrder - b.sortOrder));
    } else {
      form.reset(defaultFormData);
      setImages([]);
    }
  }, [store, form]);

  const handleSubmit = async (data: StoreFormData) => {
    // Filter out images marked for deletion and prepare for submission
    const activeImages = images
      .filter(img => !img.toDelete)
      .map((img, idx) => ({
        url: img.url,
        sortOrder: idx,
      }));
    
    // Combine opening hours into a single string
    const combinedHours: string[] = [];
    if (data.openingHours) {
      combinedHours.push(`Mon-Fri: ${data.openingHours}`);
    }
    if (data.weekendHours) {
      combinedHours.push(`Sat: ${data.weekendHours}`);
    }
    if (data.sundayHours) {
      combinedHours.push(`Sun: ${data.sundayHours}`);
    }
    
    const submitData = {
      ...data,
      openingHours: combinedHours.join(', ') || undefined,
    };
    
    await onSubmit(submitData, activeImages);
    form.reset(defaultFormData);
    setImages([]);
  };
  
  // Handle multiple image file selection
  const handleImageSelect = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const files = event.target.files;
    if (!files || files.length === 0) return;

    // Check if we can add more images
    const currentCount = images.filter(img => !img.toDelete).length;
    const availableSlots = MAX_STORE_IMAGES - currentCount;
    
    if (availableSlots <= 0) {
      toast.error(`Maximum ${MAX_STORE_IMAGES} images allowed`);
      return;
    }

    const filesToProcess = Array.from(files).slice(0, availableSlots);
    
    for (let i = 0; i < filesToProcess.length; i++) {
      const file = filesToProcess[i];
      
      // Validate file type
      if (!file.type.startsWith('image/')) {
        toast.error(`${file.name} is not an image file`);
        continue;
      }
      
      // Validate file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        toast.error(`${file.name} is too large (max 5MB)`);
        continue;
      }
      
      setUploadingIndex(i);
      
      try {
        // Convert to base64 for simple storage
        const reader = new FileReader();
        const base64 = await new Promise<string>((resolve, reject) => {
          reader.onload = (e) => resolve(e.target?.result as string);
          reader.onerror = reject;
          reader.readAsDataURL(file);
        });
        
        // Upload to server
        const response = await fetch('/api/upload', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            image: base64,
            filename: `store-${Date.now()}-${file.name.replace(/[^a-zA-Z0-9.]/g, '-')}`,
          }),
        });
        
        if (!response.ok) throw new Error('Failed to upload image');
        
        const data = await response.json();
        
        setImages(prev => [...prev, {
          url: data.url,
          sortOrder: prev.length,
          isNew: true,
        }]);
        
        toast.success(`Image ${i + 1} uploaded successfully`);
      } catch {
        toast.error(`Failed to upload ${file.name}`);
      }
      
      setUploadingIndex(null);
    }
    
    // Reset input
    if (fileInputRef.current) {
      fileInputRef.current.value = '';
    }
  };
  
  // Remove an image
  const handleRemoveImage = (index: number) => {
    setImages(prev => {
      const newImages = [...prev];
      if (newImages[index].id) {
        // Mark existing image for deletion
        newImages[index] = { ...newImages[index], toDelete: true };
      } else {
        // Remove new image directly
        newImages.splice(index, 1);
      }
      return newImages;
    });
  };
  
  // Move image up/down
  const moveImage = (index: number, direction: 'up' | 'down') => {
    const newImages = [...images];
    const newIndex = direction === 'up' ? index - 1 : index + 1;
    
    if (newIndex < 0 || newIndex >= images.length) return;
    
    [newImages[index], newImages[newIndex]] = [newImages[newIndex], newImages[index]];
    
    // Update sort orders
    newImages.forEach((img, idx) => {
      img.sortOrder = idx;
    });
    
    setImages(newImages);
  };
  
  const handleAddCategory = async () => {
    if (!newCategoryName.trim()) {
      toast.error('Category name is required');
      return;
    }
    
    setIsAddingCategory(true);
    try {
      const response = await fetch('/api/categories', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: newCategoryName.trim() }),
      });
      
      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error || 'Failed to add category');
      }
      
      toast.success('Category added successfully');
      setNewCategoryName('');
      setIsCategoryDialogOpen(false);
      
      // Refresh categories
      if (onCategoryAdded) {
        onCategoryAdded();
      }
      
      // Set the new category as selected
      form.setValue('category', newCategoryName.trim());
    } catch (error) {
      toast.error(error instanceof Error ? error.message : 'Failed to add category');
    } finally {
      setIsAddingCategory(false);
    }
  };

  const activeImages = images.filter(img => !img.toDelete);
  const canAddMoreImages = activeImages.length < MAX_STORE_IMAGES;

  return (
    <>
      <Dialog open={open} onOpenChange={onOpenChange}>
        <DialogContent className="max-w-xl max-h-[90vh] overflow-hidden flex flex-col">
          <DialogHeader>
            <DialogTitle>{store ? 'Edit Store' : 'Add New Store'}</DialogTitle>
            <DialogDescription>
              {store
                ? 'Update the store details.'
                : 'Fill in the details to add a new store.'}
            </DialogDescription>
          </DialogHeader>

          <Form {...form}>
            <form onSubmit={form.handleSubmit(handleSubmit)} className="flex-1 overflow-hidden">
              <ScrollArea className="flex-1 pr-4 -mr-4" style={{ height: 'calc(90vh - 180px)' }}>
                <div className="grid gap-4 py-4 pr-4">
                  {/* Name and Category */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="name"
                      rules={{ required: 'Name is required' }}
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Name *</FormLabel>
                          <FormControl>
                            <Input placeholder="Store name" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="category"
                      rules={{ required: 'Category is required' }}
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Category *</FormLabel>
                          <div className="flex gap-2">
                            <Select onValueChange={field.onChange} value={field.value}>
                              <FormControl>
                                <SelectTrigger>
                                  <SelectValue placeholder="Select category" />
                                </SelectTrigger>
                              </FormControl>
                              <SelectContent>
                                {categoryOptions.map((category) => (
                                  <SelectItem key={category} value={category}>
                                    {category}
                                  </SelectItem>
                                ))}
                              </SelectContent>
                            </Select>
                            <Button
                              type="button"
                              variant="outline"
                              size="icon"
                              onClick={() => setIsCategoryDialogOpen(true)}
                              title="Add new category"
                            >
                              <Plus className="w-4 h-4" />
                            </Button>
                          </div>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  {/* Description */}
                  <FormField
                    control={form.control}
                    name="description"
                    render={({ field }) => (
                      <FormItem>
                        <FormLabel>Description</FormLabel>
                        <FormControl>
                          <Textarea
                            placeholder="Brief description of the store..."
                            className="min-h-16"
                            {...field}
                          />
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  {/* Multiple Images Upload */}
                  <div className="space-y-3">
                    <div className="flex items-center justify-between">
                      <FormLabel>Store Images ({activeImages.length}/{MAX_STORE_IMAGES})</FormLabel>
                      {canAddMoreImages && (
                        <label className="cursor-pointer">
                          <input
                            type="file"
                            accept="image/*"
                            multiple
                            className="hidden"
                            ref={fileInputRef}
                            onChange={handleImageSelect}
                            disabled={uploadingIndex !== null}
                          />
                          <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={() => fileInputRef.current?.click()}
                            disabled={uploadingIndex !== null}
                          >
                            {uploadingIndex !== null ? (
                              <Loader2 className="w-4 h-4 mr-1 animate-spin" />
                            ) : (
                              <Upload className="w-4 h-4 mr-1" />
                            )}
                            Upload
                          </Button>
                        </label>
                      )}
                    </div>
                    
                    {/* Image Grid */}
                    {activeImages.length > 0 ? (
                      <div className="grid grid-cols-3 gap-3">
                        {images.map((img, index) => {
                          if (img.toDelete) return null;
                          
                          return (
                            <div
                              key={img.id || index}
                              className="relative aspect-square rounded-lg overflow-hidden border bg-muted group"
                            >
                              <img
                                src={img.url}
                                alt={`Store image ${index + 1}`}
                                className="w-full h-full object-cover"
                              />
                              
                              {/* Image number badge */}
                              <div className="absolute top-1 left-1 bg-black/60 text-white text-xs px-1.5 py-0.5 rounded">
                                {index + 1}
                              </div>
                              
                              {/* Action buttons */}
                              <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1">
                                {index > 0 && (
                                  <Button
                                    type="button"
                                    variant="secondary"
                                    size="icon"
                                    className="w-7 h-7"
                                    onClick={() => moveImage(index, 'up')}
                                  >
                                    <span className="sr-only">Move up</span>
                                    ↑
                                  </Button>
                                )}
                                <Button
                                  type="button"
                                  variant="destructive"
                                  size="icon"
                                  className="w-7 h-7"
                                  onClick={() => handleRemoveImage(index)}
                                >
                                  <X className="w-4 h-4" />
                                </Button>
                                {index < activeImages.length - 1 && (
                                  <Button
                                    type="button"
                                    variant="secondary"
                                    size="icon"
                                    className="w-7 h-7"
                                    onClick={() => moveImage(index, 'down')}
                                  >
                                    <span className="sr-only">Move down</span>
                                    ↓
                                  </Button>
                                )}
                              </div>
                            </div>
                          );
                        })}
                      </div>
                    ) : (
                      <div className="border-2 border-dashed rounded-lg p-8 text-center">
                        <ImageIcon className="w-10 h-10 mx-auto mb-2 text-muted-foreground" />
                        <p className="text-sm text-muted-foreground mb-2">
                          No images uploaded yet
                        </p>
                        <p className="text-xs text-muted-foreground">
                          Upload up to {MAX_STORE_IMAGES} images for this store
                        </p>
                      </div>
                    )}
                    
                    <p className="text-xs text-muted-foreground">
                      First image will be used as the main thumbnail. Drag to reorder.
                    </p>
                    <div className="text-xs text-muted-foreground bg-muted/50 p-2 rounded-lg">
                      <p className="font-medium mb-1">📱 Recommended sizes:</p>
                      <ul className="space-y-0.5">
                        <li>• <strong>Web:</strong> 800×800px (square)</li>
                        <li>• <strong>Mobile:</strong> 400×400px (square)</li>
                      </ul>
                      <p className="mt-1">Square images work best for store cards. Max file size: 2MB per image</p>
                    </div>
                  </div>

                  {/* Floor and Unit */}
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="floor"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Floor</FormLabel>
                          <FormControl>
                            <Input placeholder="Ground Floor" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="unitNumber"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Unit Number</FormLabel>
                          <FormControl>
                            <Input placeholder="G-12" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  {/* Status */}
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
                            <SelectItem value="OPEN">Open</SelectItem>
                            <SelectItem value="COMING_SOON">Coming Soon</SelectItem>
                            <SelectItem value="CLOSED">Closed</SelectItem>
                          </SelectContent>
                        </Select>
                        <FormMessage />
                      </FormItem>
                    )}
                  />

                  {/* Contact Info */}
                  <div className="grid grid-cols-2 gap-4">
                    <FormField
                      control={form.control}
                      name="phone"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Phone</FormLabel>
                          <FormControl>
                            <Input placeholder="+1 212-555-0100" {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />

                    <FormField
                      control={form.control}
                      name="website"
                      render={({ field }) => (
                        <FormItem>
                          <FormLabel>Website</FormLabel>
                          <FormControl>
                            <Input placeholder="https://..." {...field} />
                          </FormControl>
                          <FormMessage />
                        </FormItem>
                      )}
                    />
                  </div>

                  {/* Opening Hours */}
                  <div className="space-y-3">
                    <p className="text-sm font-medium">Opening Hours</p>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                      <FormField
                        control={form.control}
                        name="openingHours"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="text-xs text-muted-foreground">Mon-Fri</FormLabel>
                            <FormControl>
                              <Input placeholder="10:00 AM - 9:00 PM" {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      
                      <FormField
                        control={form.control}
                        name="weekendHours"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="text-xs text-muted-foreground">Saturday</FormLabel>
                            <FormControl>
                              <Input placeholder="10:00 AM - 8:00 PM" {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                      
                      <FormField
                        control={form.control}
                        name="sundayHours"
                        render={({ field }) => (
                          <FormItem>
                            <FormLabel className="text-xs text-muted-foreground">Sunday</FormLabel>
                            <FormControl>
                              <Input placeholder="11:00 AM - 6:00 PM" {...field} />
                            </FormControl>
                            <FormMessage />
                          </FormItem>
                        )}
                      />
                    </div>
                    <p className="text-xs text-muted-foreground">
                      Leave Saturday/Sunday blank if same as weekday hours.
                    </p>
                  </div>
                </div>
              </ScrollArea>

              <DialogFooter className="mt-4 pt-4 border-t">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => onOpenChange(false)}
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  disabled={isSubmitting}
                  className={isUpcoming ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600'}
                >
                  {isSubmitting && <Loader2 className="w-4 h-4 mr-2 animate-spin" />}
                  {store ? 'Update Store' : 'Add Store'}
                </Button>
              </DialogFooter>
            </form>
          </Form>
        </DialogContent>
      </Dialog>

      {/* Add New Category Dialog */}
      <Dialog open={isCategoryDialogOpen} onOpenChange={setIsCategoryDialogOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle className="flex items-center gap-2">
              <Tag className="w-5 h-5 text-emerald-500" />
              Add New Store Category
            </DialogTitle>
            <DialogDescription>
              Enter a new store category. This will be available for all stores.
            </DialogDescription>
          </DialogHeader>
          <div className="py-4">
            <Input
              placeholder="e.g., Fashion & Apparel, Electronics, Food Court"
              value={newCategoryName}
              onChange={(e) => setNewCategoryName(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === 'Enter') {
                  e.preventDefault();
                  handleAddCategory();
                }
              }}
            />
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsCategoryDialogOpen(false)}>
              Cancel
            </Button>
            <Button
              onClick={handleAddCategory}
              disabled={isAddingCategory || !newCategoryName.trim()}
              className={isUpcoming ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600'}
            >
              {isAddingCategory && <Loader2 className="w-4 h-4 mr-2 animate-spin" />}
              Add Category
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  );
}

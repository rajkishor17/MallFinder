'use client';

import { useState, useRef } from 'react';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
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
import { Switch } from '@/components/ui/switch';
import { Label } from '@/components/ui/label';
import { Loader2, Upload, X, Image as ImageIcon, ExternalLink, Building2 } from 'lucide-react';
import { toast } from 'sonner';
import type { Advertisement, AdPosition, AdType } from '@/types/advertisement';
import { AD_POSITIONS, AD_TYPES, isMallSpecificPosition } from '@/types/advertisement';
import type { Mall } from '@/types/mall';

interface AdvertisementFormProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  onSubmit: (data: AdvertisementFormData) => Promise<void>;
  advertisement?: Advertisement | null;
  isSubmitting: boolean;
  malls: Mall[];
}

export interface AdvertisementFormData {
  title: string;
  position: AdPosition;
  type: AdType;
  imageUrl: string;
  linkUrl: string;
  htmlCode: string;
  isActive: boolean;
  sortOrder: number;
  mallId: string;
}

const defaultFormData: AdvertisementFormData = {
  title: '',
  position: 'HEADER',
  type: 'IMAGE',
  imageUrl: '',
  linkUrl: '',
  htmlCode: '',
  isActive: true,
  sortOrder: 0,
  mallId: '',
};

export function AdvertisementForm({
  open,
  onOpenChange,
  onSubmit,
  advertisement,
  isSubmitting,
  malls,
}: AdvertisementFormProps) {
  const [formData, setFormData] = useState<AdvertisementFormData>(
    advertisement
      ? {
          title: advertisement.title,
          position: advertisement.position,
          type: advertisement.type,
          imageUrl: advertisement.imageUrl || '',
          linkUrl: advertisement.linkUrl || '',
          htmlCode: advertisement.htmlCode || '',
          isActive: advertisement.isActive,
          sortOrder: advertisement.sortOrder,
          mallId: advertisement.mallId || '',
        }
      : defaultFormData
  );
  const [isUploading, setIsUploading] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  // Reset form when advertisement changes
  const handleOpenChange = (open: boolean) => {
    if (open && advertisement) {
      setFormData({
        title: advertisement.title,
        position: advertisement.position,
        type: advertisement.type,
        imageUrl: advertisement.imageUrl || '',
        linkUrl: advertisement.linkUrl || '',
        htmlCode: advertisement.htmlCode || '',
        isActive: advertisement.isActive,
        sortOrder: advertisement.sortOrder,
        mallId: advertisement.mallId || '',
      });
    } else if (!open) {
      setFormData(defaultFormData);
    }
    onOpenChange(open);
  };

  // Handle image upload
  const handleImageUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

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

    setIsUploading(true);
    try {
      // Convert to base64
      const reader = new FileReader();
      reader.onload = async () => {
        try {
          const base64 = reader.result as string;

          // Upload image
          const response = await fetch('/api/upload', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              image: base64,
              type: 'ads',
              filename: `ad-${Date.now()}-${file.name.replace(/[^a-zA-Z0-9.]/g, '-')}`,
            }),
          });

          if (!response.ok) throw new Error('Failed to upload image');
          const { url } = await response.json();

          setFormData((prev) => ({ ...prev, imageUrl: url }));
          toast.success('Image uploaded successfully');
        } catch {
          toast.error('Failed to upload image');
        } finally {
          setIsUploading(false);
        }
      };
      reader.readAsDataURL(file);
    } catch {
      toast.error('Failed to read image file');
      setIsUploading(false);
    }

    // Reset input
    e.target.value = '';
  };

  // Handle form submission
  const handleSubmit = async () => {
    // Validate required fields
    if (!formData.title.trim()) {
      toast.error('Title is required');
      return;
    }

    if (formData.type === 'IMAGE' && !formData.imageUrl) {
      toast.error('Image is required for image type ads');
      return;
    }

    if (formData.type === 'HTML' && !formData.htmlCode.trim()) {
      toast.error('HTML code is required for HTML type ads');
      return;
    }

    // Validate mall selection for mall-specific positions
    if (isMallSpecificPosition(formData.position) && !formData.mallId) {
      toast.error('Please select a mall for mall-specific ad positions');
      return;
    }

    await onSubmit(formData);
  };

  // Check if current position is mall-specific
  const isMallAd = isMallSpecificPosition(formData.position);
  const selectedPosition = AD_POSITIONS.find((p) => p.value === formData.position);

  return (
    <Dialog open={open} onOpenChange={handleOpenChange}>
      <DialogContent className="max-w-lg max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            {advertisement ? (
              <>
                <ImageIcon className="w-5 h-5 text-emerald-500" />
                Edit Advertisement
              </>
            ) : (
              <>
                <Upload className="w-5 h-5 text-emerald-500" />
                Add Advertisement
              </>
            )}
          </DialogTitle>
          <DialogDescription>
            Configure the advertisement. Choose between image with link or custom HTML code.
          </DialogDescription>
        </DialogHeader>

        <div className="space-y-4 py-4">
          {/* Title */}
          <div className="space-y-2">
            <Label htmlFor="title">Title *</Label>
            <Input
              id="title"
              placeholder="e.g., Summer Sale Banner"
              value={formData.title}
              onChange={(e) => setFormData({ ...formData, title: e.target.value })}
            />
          </div>

          {/* Position and Mall */}
          <div className="grid grid-cols-2 gap-4">
            <div className="space-y-2">
              <Label>Position</Label>
              <Select
                value={formData.position}
                onValueChange={(value: AdPosition) =>
                  setFormData({ ...formData, position: value, mallId: '' })
                }
              >
                <SelectTrigger>
                  <SelectValue placeholder="Select position" />
                </SelectTrigger>
                <SelectContent>
                  {AD_POSITIONS.map((pos) => (
                    <SelectItem key={pos.value} value={pos.value}>
                      <div className="flex flex-col">
                        <span>{pos.label}</span>
                      </div>
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              {selectedPosition && (
                <p className="text-xs text-muted-foreground">{selectedPosition.description}</p>
              )}
            </div>

            {/* Mall Selection - only show for mall-specific positions */}
            {isMallAd && (
              <div className="space-y-2">
                <Label>Select Mall *</Label>
                <Select
                  value={formData.mallId}
                  onValueChange={(value) => setFormData({ ...formData, mallId: value })}
                >
                  <SelectTrigger>
                    <SelectValue placeholder="Select a mall" />
                  </SelectTrigger>
                  <SelectContent>
                    {malls.map((mall) => (
                      <SelectItem key={mall.id} value={mall.id}>
                        <div className="flex items-center gap-2">
                          <Building2 className="w-4 h-4" />
                          {mall.name}
                        </div>
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            )}
          </div>

          {/* Ad Type */}
          <div className="space-y-2">
            <Label>Type</Label>
            <Select
              value={formData.type}
              onValueChange={(value: AdType) => setFormData({ ...formData, type: value })}
            >
              <SelectTrigger>
                <SelectValue placeholder="Select type" />
              </SelectTrigger>
              <SelectContent>
                {AD_TYPES.map((type) => (
                  <SelectItem key={type.value} value={type.value}>
                    {type.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* Image Type Fields */}
          {formData.type === 'IMAGE' && (
            <>
              {/* Image Upload */}
              <div className="space-y-2">
                <Label>Advertisement Image *</Label>
                <div className="flex gap-2">
                  <Input
                    placeholder="Enter image URL or upload"
                    value={formData.imageUrl}
                    onChange={(e) => setFormData({ ...formData, imageUrl: e.target.value })}
                    className="flex-1"
                  />
                  <input
                    type="file"
                    accept="image/*"
                    className="hidden"
                    ref={fileInputRef}
                    onChange={handleImageUpload}
                    disabled={isUploading}
                  />
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => fileInputRef.current?.click()}
                    disabled={isUploading}
                  >
                    {isUploading ? (
                      <Loader2 className="w-4 h-4 animate-spin" />
                    ) : (
                      <Upload className="w-4 h-4" />
                    )}
                  </Button>
                </div>
              </div>

              {/* Image Preview */}
              {formData.imageUrl && (
                <div className="relative rounded-lg overflow-hidden border bg-muted">
                  <img
                    src={formData.imageUrl}
                    alt="Advertisement preview"
                    className="w-full h-40 object-contain bg-white"
                  />
                  <Button
                    type="button"
                    variant="destructive"
                    size="icon"
                    className="absolute top-2 right-2 w-6 h-6"
                    onClick={() => setFormData({ ...formData, imageUrl: '' })}
                  >
                    <X className="w-4 h-4" />
                  </Button>
                </div>
              )}

              {/* Link URL */}
              <div className="space-y-2">
                <Label htmlFor="linkUrl">Link URL (opens in new tab)</Label>
                <div className="flex gap-2">
                  <Input
                    id="linkUrl"
                    placeholder="https://example.com/promo"
                    value={formData.linkUrl}
                    onChange={(e) => setFormData({ ...formData, linkUrl: e.target.value })}
                  />
                  {formData.linkUrl && (
                    <Button
                      type="button"
                      variant="outline"
                      size="icon"
                      onClick={() => window.open(formData.linkUrl, '_blank')}
                      title="Test link"
                    >
                      <ExternalLink className="w-4 h-4" />
                    </Button>
                  )}
                </div>
                <p className="text-xs text-muted-foreground">
                  When users click the image, this URL will open in a new window
                </p>
              </div>
            </>
          )}

          {/* HTML Type Fields */}
          {formData.type === 'HTML' && (
            <div className="space-y-2">
              <Label htmlFor="htmlCode">HTML Code *</Label>
              <Textarea
                id="htmlCode"
                placeholder="<div>Your HTML code here...</div>"
                className="min-h-32 font-mono text-sm"
                value={formData.htmlCode}
                onChange={(e) => setFormData({ ...formData, htmlCode: e.target.value })}
              />
              <p className="text-xs text-muted-foreground">
                Paste your custom HTML/JavaScript code for the advertisement
              </p>
            </div>
          )}

          {/* Sort Order */}
          <div className="space-y-2">
            <Label htmlFor="sortOrder">Sort Order</Label>
            <Input
              id="sortOrder"
              type="number"
              min="0"
              value={formData.sortOrder}
              onChange={(e) =>
                setFormData({ ...formData, sortOrder: parseInt(e.target.value) || 0 })
              }
            />
            <p className="text-xs text-muted-foreground">
              Lower numbers appear first. Multiple ads in the same position are sorted by this value.
            </p>
          </div>

          {/* Active Status */}
          <div className="flex items-center justify-between">
            <div>
              <Label htmlFor="isActive">Active</Label>
              <p className="text-xs text-muted-foreground">
                Only active ads will be displayed on the website
              </p>
            </div>
            <Switch
              id="isActive"
              checked={formData.isActive}
              onCheckedChange={(checked) => setFormData({ ...formData, isActive: checked })}
            />
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" onClick={() => handleOpenChange(false)}>
            Cancel
          </Button>
          <Button
            onClick={handleSubmit}
            disabled={isSubmitting || isUploading}
            className="bg-emerald-500 hover:bg-emerald-600"
          >
            {isSubmitting && <Loader2 className="w-4 h-4 mr-2 animate-spin" />}
            {advertisement ? 'Update' : 'Create'} Advertisement
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}

'use client';

import { useState, useRef } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Upload, Loader2, Save } from 'lucide-react';
import { toast } from 'sonner';

interface Settings {
  siteName: string;
  siteTagline: string;
  siteLogo: string;
}

interface SettingsDialogContentProps {
  settings: Settings;
  onUpdate: (settings: Settings) => void;
}

export function SettingsDialogContent({ settings, onUpdate }: SettingsDialogContentProps) {
  const [formData, setFormData] = useState<Settings>(settings);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isUploading, setIsUploading] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const handleSave = async () => {
    setIsSubmitting(true);
    try {
      const response = await fetch('/api/settings', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });

      if (!response.ok) throw new Error('Failed to save settings');

      toast.success('Settings saved successfully');
      onUpdate(formData);
    } catch (error) {
      toast.error('Failed to save settings');
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleLogoUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
      toast.error('Please select an image file');
      return;
    }

    if (file.size > 2 * 1024 * 1024) {
      toast.error('Image size should be less than 2MB');
      return;
    }

    setIsUploading(true);
    try {
      const reader = new FileReader();
      reader.onload = async () => {
        try {
          const base64 = reader.result as string;
          const uploadResponse = await fetch('/api/upload', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              image: base64,
              type: 'settings',
            }),
          });

          if (!uploadResponse.ok) throw new Error('Failed to upload image');
          const { url } = await uploadResponse.json();

          setFormData({ ...formData, siteLogo: url });
          toast.success('Logo uploaded successfully');
        } catch {
          toast.error('Failed to upload logo');
        } finally {
          setIsUploading(false);
        }
      };
      reader.readAsDataURL(file);
    } catch {
      toast.error('Failed to read image file');
      setIsUploading(false);
    }

    e.target.value = '';
  };

  return (
    <div className="space-y-6 py-4">
      <div className="space-y-2">
        <Label htmlFor="siteName">Site Name</Label>
        <Input
          id="siteName"
          value={formData.siteName}
          onChange={(e) => setFormData({ ...formData, siteName: e.target.value })}
          placeholder="MallFinder"
        />
      </div>

      <div className="space-y-2">
        <Label htmlFor="siteTagline">Site Tagline</Label>
        <Input
          id="siteTagline"
          value={formData.siteTagline}
          onChange={(e) => setFormData({ ...formData, siteTagline: e.target.value })}
          placeholder="Discover Shopping Destinations"
        />
      </div>

      <div className="space-y-2">
        <Label>Site Logo</Label>
        <div className="flex items-center gap-4">
          {formData.siteLogo && (
            <div className="w-16 h-16 border rounded-lg overflow-hidden">
              <img
                src={formData.siteLogo}
                alt="Site logo"
                className="w-full h-full object-contain"
              />
            </div>
          )}
          <div className="flex-1">
            <input
              type="file"
              accept="image/*"
              ref={fileInputRef}
              onChange={handleLogoUpload}
              className="hidden"
            />
            <Button
              type="button"
              variant="outline"
              onClick={() => fileInputRef.current?.click()}
              disabled={isUploading}
            >
              {isUploading ? (
                <Loader2 className="w-4 h-4 mr-2 animate-spin" />
              ) : (
                <Upload className="w-4 h-4 mr-2" />
              )}
              {isUploading ? 'Uploading...' : 'Upload Logo'}
            </Button>
            {formData.siteLogo && (
              <Button
                type="button"
                variant="ghost"
                size="sm"
                onClick={() => setFormData({ ...formData, siteLogo: '' })}
                className="ml-2 text-destructive hover:text-destructive"
              >
                Remove
              </Button>
            )}
          </div>
        </div>
        <p className="text-xs text-muted-foreground">
          📱 Recommended sizes: <strong>Web:</strong> 200×200px |
          <strong> Mobile:</strong> 100×100px. Square image, max 2MB
        </p>
      </div>

      <div className="flex justify-end gap-2 pt-4 border-t">
        <Button
          onClick={handleSave}
          disabled={isSubmitting}
          className="bg-emerald-500 hover:bg-emerald-600"
        >
          {isSubmitting ? (
            <Loader2 className="w-4 h-4 mr-2 animate-spin" />
          ) : (
            <Save className="w-4 h-4 mr-2" />
          )}
          Save Settings
        </Button>
      </div>
    </div>
  );
}

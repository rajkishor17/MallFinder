'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import Image from 'next/image';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import {
  ArrowLeft,
  Building2,
  Loader2,
  LogOut,
  User,
  Save,
  Globe,
  Upload,
  Share2,
  Facebook,
  Instagram,
  Linkedin,
  Youtube,
  MessageCircle,
} from 'lucide-react';

// Custom X (formerly Twitter) icon
const XIcon = ({ className }: { className?: string }) => (
  <svg
    viewBox="0 0 24 24"
    className={className}
    fill="currentColor"
  >
    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
  </svg>
);

// Custom Pinterest icon
const PinterestIcon = ({ className }: { className?: string }) => (
  <svg
    viewBox="0 0 24 24"
    className={className}
    fill="currentColor"
  >
    <path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z" />
  </svg>
);
import { toast } from 'sonner';

interface SiteSettings {
  siteName: string;
  siteTagline: string;
  siteLogo: string;
  // Social Links
  facebook: string;
  twitter: string;
  instagram: string;
  linkedin: string;
  youtube: string;
  whatsapp: string;
  pinterest: string;
}

export default function SettingsManagement() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [authUser, setAuthUser] = useState<{ email: string; name: string | null } | null>(null);

  const [settings, setSettings] = useState<SiteSettings>({
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

  const [isUploadingLogo, setIsUploadingLogo] = useState(false);

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

  // Fetch settings
  const fetchSettings = useCallback(async () => {
    try {
      const response = await fetch('/api/settings');
      if (response.ok) {
        const data = await response.json();
        setSettings({
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
      console.error('Failed to load settings');
    }
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      setLoading(true);
      fetchSettings().finally(() => setLoading(false));
    }
  }, [isAuthenticated, fetchSettings]);

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

  // Handle logo upload
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

    setIsUploadingLogo(true);
    const reader = new FileReader();
    reader.onload = async () => {
      try {
        const base64 = reader.result as string;
        const uploadResponse = await fetch('/api/upload', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ image: base64, type: 'logos' }),
        });

        if (!uploadResponse.ok) throw new Error('Failed to upload image');
        const { url } = await uploadResponse.json();
        setSettings({ ...settings, siteLogo: url });
        toast.success('Logo uploaded successfully');
      } catch {
        toast.error('Failed to upload logo');
      } finally {
        setIsUploadingLogo(false);
      }
    };
    reader.readAsDataURL(file);
    e.target.value = '';
  };

  // Save settings
  const handleSave = async () => {
    setSaving(true);
    try {
      const response = await fetch('/api/settings', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(settings),
        credentials: 'include',
      });

      if (!response.ok) throw new Error('Failed to save settings');

      toast.success('Settings saved successfully');
    } catch {
      toast.error('Failed to save settings');
    } finally {
      setSaving(false);
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
                  Site Settings
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

      <main className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <Tabs defaultValue="identity" className="space-y-6">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="identity" className="flex items-center gap-2">
              <Globe className="w-4 h-4" />
              Site Identity
            </TabsTrigger>
            <TabsTrigger value="social" className="flex items-center gap-2">
              <Share2 className="w-4 h-4" />
              Social Links
            </TabsTrigger>
          </TabsList>

          {/* Site Identity Tab */}
          <TabsContent value="identity">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Globe className="w-5 h-5" />
                  Site Identity
                </CardTitle>
                <CardDescription>
                  Configure your site name, tagline, and logo
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <label className="text-sm font-medium mb-2 block">Site Name</label>
                  <Input
                    value={settings.siteName}
                    onChange={(e) => setSettings({ ...settings, siteName: e.target.value })}
                    placeholder="MallFinder"
                  />
                  <p className="text-xs text-muted-foreground mt-1">
                    This name appears in the header and browser tab.
                  </p>
                </div>

                <div>
                  <label className="text-sm font-medium mb-2 block">Site Tagline</label>
                  <Input
                    value={settings.siteTagline}
                    onChange={(e) => setSettings({ ...settings, siteTagline: e.target.value })}
                    placeholder="Discover Shopping Destinations"
                  />
                  <p className="text-xs text-muted-foreground mt-1">
                    A short description that appears below the site name.
                  </p>
                </div>

                <div>
                  <label className="text-sm font-medium mb-2 block">Site Logo</label>
                  <div className="flex gap-2 items-start">
                    <Input
                      value={settings.siteLogo}
                      onChange={(e) => setSettings({ ...settings, siteLogo: e.target.value })}
                      placeholder="/logo.png"
                      className="flex-1"
                    />
                    <label className="cursor-pointer">
                      <input
                        type="file"
                        accept="image/*"
                        onChange={handleLogoUpload}
                        className="hidden"
                      />
                      <Button variant="outline" type="button" disabled={isUploadingLogo}>
                        {isUploadingLogo ? (
                          <Loader2 className="w-4 h-4 animate-spin" />
                        ) : (
                          <Upload className="w-4 h-4" />
                        )}
                      </Button>
                    </label>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">
                    Upload a logo or enter a URL. Recommended size: 200x50px.
                  </p>

                  {settings.siteLogo && (
                    <div className="mt-4 p-4 border rounded-lg bg-slate-50 dark:bg-slate-800">
                      <p className="text-xs text-muted-foreground mb-2">Logo Preview:</p>
                      <div className="relative w-40 h-12">
                        <Image
                          src={settings.siteLogo}
                          alt="Site Logo"
                          fill
                          className="object-contain"
                        />
                      </div>
                    </div>
                  )}
                </div>
              </CardContent>
            </Card>

            {/* Preview Card */}
            <Card className="mt-6">
              <CardHeader>
                <CardTitle>Preview</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex items-center gap-3 p-4 bg-white dark:bg-slate-800 rounded-lg border">
                  {settings.siteLogo ? (
                    <Image
                      src={settings.siteLogo}
                      alt="Logo"
                      width={40}
                      height={40}
                      className="object-contain"
                    />
                  ) : (
                    <div className="bg-gradient-to-r from-emerald-500 to-teal-500 p-2 rounded-lg">
                      <Building2 className="w-5 h-5 text-white" />
                    </div>
                  )}
                  <div>
                    <p className="font-semibold">{settings.siteName}</p>
                    <p className="text-xs text-muted-foreground">{settings.siteTagline}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          {/* Social Links Tab */}
          <TabsContent value="social">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Share2 className="w-5 h-5" />
                  Social Media Links
                </CardTitle>
                <CardDescription>
                  Add your social media links. These will appear in the header and footer.
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                {/* Facebook */}
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <Facebook className="w-5 h-5 text-white" />
                  </div>
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-1 block">Facebook</label>
                    <Input
                      value={settings.facebook}
                      onChange={(e) => setSettings({ ...settings, facebook: e.target.value })}
                      placeholder="https://facebook.com/yourpage"
                    />
                  </div>
                </div>

                {/* X (formerly Twitter) */}
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center flex-shrink-0">
                    <XIcon className="w-5 h-5 text-white" />
                  </div>
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-1 block">X</label>
                    <Input
                      value={settings.twitter}
                      onChange={(e) => setSettings({ ...settings, twitter: e.target.value })}
                      placeholder="https://x.com/yourhandle"
                    />
                  </div>
                </div>

                {/* Instagram */}
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-full bg-gradient-to-br from-purple-600 to-pink-500 flex items-center justify-center flex-shrink-0">
                    <Instagram className="w-5 h-5 text-white" />
                  </div>
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-1 block">Instagram</label>
                    <Input
                      value={settings.instagram}
                      onChange={(e) => setSettings({ ...settings, instagram: e.target.value })}
                      placeholder="https://instagram.com/yourhandle"
                    />
                  </div>
                </div>

                {/* LinkedIn */}
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <Linkedin className="w-5 h-5 text-white" />
                  </div>
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-1 block">LinkedIn</label>
                    <Input
                      value={settings.linkedin}
                      onChange={(e) => setSettings({ ...settings, linkedin: e.target.value })}
                      placeholder="https://linkedin.com/company/yourcompany"
                    />
                  </div>
                </div>

                {/* YouTube */}
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center flex-shrink-0">
                    <Youtube className="w-5 h-5 text-white" />
                  </div>
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-1 block">YouTube</label>
                    <Input
                      value={settings.youtube}
                      onChange={(e) => setSettings({ ...settings, youtube: e.target.value })}
                      placeholder="https://youtube.com/@yourchannel"
                    />
                  </div>
                </div>

                {/* WhatsApp */}
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center flex-shrink-0">
                    <MessageCircle className="w-5 h-5 text-white" />
                  </div>
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-1 block">WhatsApp</label>
                    <Input
                      value={settings.whatsapp}
                      onChange={(e) => setSettings({ ...settings, whatsapp: e.target.value })}
                      placeholder="919876543210 (with country code, no +)"
                    />
                    <p className="text-xs text-muted-foreground mt-1">
                      Enter phone number with country code (e.g., 919876543210)
                    </p>
                  </div>
                </div>

                {/* Pinterest */}
                <div className="flex items-center gap-4">
                  <div className="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center flex-shrink-0">
                    <PinterestIcon className="w-5 h-5 text-white" />
                  </div>
                  <div className="flex-1">
                    <label className="text-sm font-medium mb-1 block">Pinterest</label>
                    <Input
                      value={settings.pinterest}
                      onChange={(e) => setSettings({ ...settings, pinterest: e.target.value })}
                      placeholder="https://pinterest.com/yourprofile"
                    />
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Social Preview */}
            <Card className="mt-6">
              <CardHeader>
                <CardTitle>Social Links Preview</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="flex items-center gap-3 p-4 bg-white dark:bg-slate-800 rounded-lg border">
                  <div className="flex items-center gap-2">
                    {(settings.facebook || settings.twitter || settings.instagram || settings.linkedin || settings.youtube || settings.whatsapp || settings.pinterest) ? (
                      <>
                        {settings.facebook && (
                          <div className="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                            <Facebook className="w-5 h-5 text-white" />
                          </div>
                        )}
                        {settings.twitter && (
                          <div className="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center">
                            <XIcon className="w-5 h-5 text-white" />
                          </div>
                        )}
                        {settings.instagram && (
                          <div className="w-10 h-10 rounded-full bg-gradient-to-br from-purple-600 to-pink-500 flex items-center justify-center">
                            <Instagram className="w-5 h-5 text-white" />
                          </div>
                        )}
                        {settings.linkedin && (
                          <div className="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                            <Linkedin className="w-5 h-5 text-white" />
                          </div>
                        )}
                        {settings.youtube && (
                          <div className="w-10 h-10 rounded-full bg-red-500 flex items-center justify-center">
                            <Youtube className="w-5 h-5 text-white" />
                          </div>
                        )}
                        {settings.whatsapp && (
                          <div className="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center">
                            <MessageCircle className="w-5 h-5 text-white" />
                          </div>
                        )}
                        {settings.pinterest && (
                          <div className="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center">
                            <PinterestIcon className="w-5 h-5 text-white" />
                          </div>
                        )}
                      </>
                    ) : (
                      <p className="text-sm text-muted-foreground">No social links configured yet</p>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>

        {/* Save Button */}
        <div className="flex justify-end mt-6">
          <Button
            onClick={handleSave}
            disabled={saving}
            className="bg-emerald-500 hover:bg-emerald-600"
          >
            {saving ? (
              <>
                <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                Saving...
              </>
            ) : (
              <>
                <Save className="w-4 h-4 mr-2" />
                Save Settings
              </>
            )}
          </Button>
        </div>
      </main>
    </div>
  );
}

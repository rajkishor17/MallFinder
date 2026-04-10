'use client';

import { useState, useEffect, useMemo } from 'react';
import Link from 'next/link';
import Image from 'next/image';
import { Building2, Menu, X } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { SocialLinks, SocialLinksSkeleton } from '@/components/ui/social-links';

interface SiteHeaderProps {
  isAdmin?: boolean;
  children?: React.ReactNode;
  siteName?: string;
  siteTagline?: string;
  siteLogo?: string;
}

interface SocialSettings {
  facebook?: string;
  twitter?: string;
  instagram?: string;
  linkedin?: string;
  youtube?: string;
  whatsapp?: string;
}

export function SiteHeader({ isAdmin = false, children, siteName: propSiteName, siteTagline: propSiteTagline, siteLogo: propSiteLogo }: SiteHeaderProps) {
  const [fetchedSettings, setFetchedSettings] = useState<{
    siteName?: string;
    siteTagline?: string;
    siteLogo?: string;
    social?: SocialSettings;
  }>({});
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
    // Only fetch if props aren't provided
    if (!propSiteName && !propSiteTagline && !propSiteLogo) {
      const fetchSettings = async () => {
        try {
          const response = await fetch('/api/settings');
          if (response.ok) {
            const data = await response.json();
            setFetchedSettings({
              siteName: data.siteName || 'MallFinder',
              siteTagline: data.siteTagline || 'Discover Shopping Destinations',
              siteLogo: data.siteLogo || '',
              social: {
                facebook: data.facebook || '',
                twitter: data.twitter || '',
                instagram: data.instagram || '',
                linkedin: data.linkedin || '',
                youtube: data.youtube || '',
                whatsapp: data.whatsapp || '',
              },
            });
          }
        } catch (error) {
          console.error('Failed to fetch settings:', error);
        }
      };
      fetchSettings();
    }
  }, [propSiteName, propSiteTagline, propSiteLogo]);

  // Use props if provided, otherwise use fetched settings
  const settings = useMemo(() => ({
    siteName: propSiteName || fetchedSettings.siteName || 'MallFinder',
    siteTagline: propSiteTagline || fetchedSettings.siteTagline || 'Discover Shopping Destinations',
    siteLogo: propSiteLogo || fetchedSettings.siteLogo || '',
    social: fetchedSettings.social || {},
  }), [propSiteName, propSiteTagline, propSiteLogo, fetchedSettings]);

  const hasSocialLinks = settings.social && (
    settings.social.facebook ||
    settings.social.twitter ||
    settings.social.instagram ||
    settings.social.linkedin ||
    settings.social.youtube ||
    settings.social.whatsapp
  );

  return (
    <header className="bg-white dark:bg-slate-900 border-b shadow-sm sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          <Link href={isAdmin ? "/admin" : "/"} className="flex items-center gap-2 sm:gap-3">
            {settings.siteLogo ? (
              <div className="relative w-10 h-10 sm:w-12 sm:h-12">
                <Image
                  src={settings.siteLogo}
                  alt={settings.siteName}
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
                {settings.siteName}
              </h1>
              {!isAdmin && (
                <p className="text-xs text-muted-foreground hidden sm:block">
                  {settings.siteTagline}
                </p>
              )}
              {isAdmin && (
                <p className="text-xs text-muted-foreground hidden sm:block">
                  Shopping Malls Management Dashboard
                </p>
              )}
            </div>
          </Link>

          <div className="flex items-center gap-2 sm:gap-4">
            {/* Social Links - Hidden on mobile */}
            {!isAdmin && hasSocialLinks && (
              <div className="hidden lg:flex items-center">
                {fetchedSettings.social ? (
                  <SocialLinks links={fetchedSettings.social} iconSize="sm" />
                ) : (
                  <SocialLinksSkeleton />
                )}
              </div>
            )}

            {/* Desktop Navigation */}
            <nav className="hidden sm:flex items-center gap-1 sm:gap-2">
              {children}
            </nav>

            {/* Mobile Menu Button */}
            {children && (
              <Button
                variant="ghost"
                size="icon"
                className="sm:hidden"
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              >
                {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
              </Button>
            )}
          </div>
        </div>

        {/* Mobile Navigation Menu */}
        {children && mobileMenuOpen && (
          <nav className="sm:hidden pb-4 border-t pt-2">
            {/* Social Links in Mobile Menu */}
            {!isAdmin && hasSocialLinks && fetchedSettings.social && (
              <div className="mb-3 pb-3 border-b">
                <p className="text-xs text-muted-foreground mb-2">Follow us:</p>
                <SocialLinks links={fetchedSettings.social} iconSize="sm" />
              </div>
            )}
            <div className="flex flex-col gap-1">
              {children}
            </div>
          </nav>
        )}
      </div>
    </header>
  );
}

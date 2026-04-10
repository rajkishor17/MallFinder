'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { Building2, MapPin, Mail, Phone, Clock } from 'lucide-react';
import { SocialLinks, SocialLinksSkeleton } from '@/components/ui/social-links';

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
}

export function SiteFooter() {
  const [settings, setSettings] = useState<SiteSettings>({
    siteName: 'MallFinder',
    siteTagline: 'Discover Shopping Destinations',
    siteLogo: '',
  });
  const [loading, setLoading] = useState(true);
  const currentYear = new Date().getFullYear();

  useEffect(() => {
    const fetchSettings = async () => {
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
          });
        }
      } catch (error) {
        console.error('Failed to fetch settings:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchSettings();
  }, []);

  const socialLinks = {
    facebook: settings.facebook,
    twitter: settings.twitter,
    instagram: settings.instagram,
    linkedin: settings.linkedin,
    youtube: settings.youtube,
    whatsapp: settings.whatsapp,
  };

  const hasSocialLinks = Object.values(socialLinks).some(Boolean);

  return (
    <footer className="bg-slate-900 text-white">
      {/* Main Footer */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* Brand Section */}
          <div className="lg:col-span-1">
            <div className="flex items-center gap-3 mb-4">
              {settings.siteLogo ? (
                <img 
                  src={settings.siteLogo} 
                  alt={settings.siteName}
                  className="h-10 w-auto object-contain"
                />
              ) : (
                <div className="bg-gradient-to-r from-emerald-500 to-teal-500 p-2 rounded-lg">
                  <Building2 className="w-6 h-6 text-white" />
                </div>
              )}
              <div>
                <h3 className="text-xl font-bold">{settings.siteName}</h3>
                <p className="text-xs text-slate-400">{settings.siteTagline}</p>
              </div>
            </div>
            <p className="text-sm text-slate-400 mb-4">
              Your ultimate destination for discovering shopping malls, stores, and retail experiences across India.
            </p>
            
            {/* Social Links */}
            {hasSocialLinks && (
              <div>
                <p className="text-sm font-medium mb-3">Follow us:</p>
                {loading ? (
                  <SocialLinksSkeleton />
                ) : (
                  <SocialLinks links={socialLinks} iconSize="sm" />
                )}
              </div>
            )}
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-lg font-semibold mb-4">Quick Links</h4>
            <ul className="space-y-2">
              <li>
                <Link href="/" className="text-slate-400 hover:text-white transition-colors text-sm flex items-center gap-2">
                  <MapPin className="w-4 h-4" />
                  Home
                </Link>
              </li>
              <li>
                <Link href="/about" className="text-slate-400 hover:text-white transition-colors text-sm flex items-center gap-2">
                  <Building2 className="w-4 h-4" />
                  About Us
                </Link>
              </li>
              <li>
                <Link href="/contact" className="text-slate-400 hover:text-white transition-colors text-sm flex items-center gap-2">
                  <Mail className="w-4 h-4" />
                  Contact Us
                </Link>
              </li>
            </ul>
          </div>

          {/* Categories */}
          <div>
            <h4 className="text-lg font-semibold mb-4">Categories</h4>
            <ul className="space-y-2">
              <li>
                <Link href="/?category=existing" className="text-slate-400 hover:text-white transition-colors text-sm">
                  Existing Malls
                </Link>
              </li>
              <li>
                <Link href="/?category=upcoming" className="text-slate-400 hover:text-white transition-colors text-sm">
                  Upcoming Malls
                </Link>
              </li>
            </ul>
          </div>

          {/* Contact Info */}
          <div>
            <h4 className="text-lg font-semibold mb-4">Contact</h4>
            <ul className="space-y-3">
              <li className="flex items-start gap-3 text-sm text-slate-400">
                <MapPin className="w-4 h-4 mt-0.5 flex-shrink-0" />
                <span>Discover malls across India</span>
              </li>
              <li className="flex items-center gap-3 text-sm text-slate-400">
                <Clock className="w-4 h-4 flex-shrink-0" />
                <span>Available 24/7</span>
              </li>
              {settings.whatsapp && (
                <li>
                  <a 
                    href={`https://wa.me/${settings.whatsapp.replace(/\D/g, '')}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-3 text-sm text-slate-400 hover:text-white transition-colors"
                  >
                    <Phone className="w-4 h-4 flex-shrink-0" />
                    <span>Chat on WhatsApp</span>
                  </a>
                </li>
              )}
            </ul>
          </div>
        </div>
      </div>

      {/* Bottom Bar */}
      <div className="border-t border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p className="text-sm text-slate-400">
              © {currentYear} {settings.siteName}. All rights reserved.
            </p>
            <div className="flex items-center gap-4">
              <Link href="/about" className="text-sm text-slate-400 hover:text-white transition-colors">
                About
              </Link>
              <Link href="/contact" className="text-sm text-slate-400 hover:text-white transition-colors">
                Contact
              </Link>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}

'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Building2, ArrowLeft, Loader2 } from 'lucide-react';
import { SiteHeader } from '@/components/layout/SiteHeader';
import { AdvertisementBanner } from '@/components/advertisement/AdvertisementBanner';
import type { Page } from '@/types/page';

interface PublicPageProps {
  slug?: string;
  page?: Page;
}

export function PublicPage({ slug, page: initialPage }: PublicPageProps) {
  const [page, setPage] = useState<Page | null>(initialPage || null);
  const [loading, setLoading] = useState(!initialPage);

  useEffect(() => {
    // If page is provided as prop, use it
    if (initialPage) {
      setPage(initialPage);
      setLoading(false);
      return;
    }
    
    // Otherwise fetch by slug
    if (!slug) {
      setLoading(false);
      return;
    }
    
    const fetchPage = async () => {
      try {
        const response = await fetch(`/api/pages/${slug}`);
        if (response.ok) {
          const data = await response.json();
          setPage(data);
        }
      } catch (error) {
        console.error('Failed to fetch page:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchPage();
  }, [slug, initialPage]);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loader2 className="w-8 h-8 animate-spin text-emerald-500" />
      </div>
    );
  }

  if (!page) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900">
        <SiteHeader>
          <Link href="/">
            <Button variant="ghost" size="sm">Home</Button>
          </Link>
        </SiteHeader>
        <main className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
          <h1 className="text-2xl font-bold mb-4">Page Not Found</h1>
          <p className="text-muted-foreground mb-6">The page you're looking for doesn't exist.</p>
          <Link href="/">
            <Button variant="outline">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back to Home
            </Button>
          </Link>
        </main>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900">
      {/* Header Advertisement */}
      <AdvertisementBanner position="HEADER" />
      
      {/* Header */}
      <SiteHeader>
        <Link href="/">
          <Button variant="ghost" size="sm">Home</Button>
        </Link>
        <Link href="/about">
          <Button variant="ghost" size="sm">About Us</Button>
        </Link>
        <Link href="/contact">
          <Button variant="ghost" size="sm">Contact Us</Button>
        </Link>
      </SiteHeader>

      {/* Page Content */}
      <main className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <Card>
          <CardContent className="p-8">
            <h1 className="text-3xl font-bold mb-6 bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
              {page.title}
            </h1>
            <div className="prose prose-slate dark:prose-invert max-w-none">
              <div className="whitespace-pre-wrap text-slate-700 dark:text-slate-300 leading-relaxed">
                {page.content}
              </div>
            </div>
          </CardContent>
        </Card>

        <div className="mt-8 text-center">
          <Link href="/">
            <Button variant="outline">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Back to Home
            </Button>
          </Link>
        </div>
      </main>

      {/* Footer Advertisement */}
      <AdvertisementBanner position="FOOTER" />

      {/* Footer */}
      <footer className="bg-white dark:bg-slate-900 border-t mt-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div className="flex items-center gap-3">
              <div className="bg-gradient-to-r from-emerald-500 to-teal-500 p-2 rounded-lg">
                <Building2 className="w-5 h-5 text-white" />
              </div>
              <span className="font-semibold">MallFinder</span>
            </div>
            <div className="flex items-center gap-6 text-sm text-muted-foreground">
              <Link href="/" className="hover:text-foreground transition-colors">
                Home
              </Link>
              <Link href="/about" className="hover:text-foreground transition-colors">
                About Us
              </Link>
              <Link href="/contact" className="hover:text-foreground transition-colors">
                Contact Us
              </Link>
            </div>
            <p className="text-sm text-muted-foreground">
              © 2026 MallFinder. All rights reserved.
            </p>
          </div>
        </div>
      </footer>
    </div>
  );
}

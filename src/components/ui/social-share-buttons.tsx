'use client';

import { useState } from 'react';
import { 
  Facebook, 
  Twitter, 
  Linkedin, 
  MessageCircle,
  Share2,
  Link2,
  Check
} from 'lucide-react';
import { motion, AnimatePresence } from 'framer-motion';
import { Button } from '@/components/ui/button';
import { toast } from 'sonner';

interface SocialShareButtonsProps {
  title?: string;
  description?: string;
  url?: string;
  className?: string;
  variant?: 'icons' | 'buttons' | 'compact';
  size?: 'sm' | 'md';
}

export function SocialShareButtons({ 
  title,
  description = '',
  url,
  className = '',
  variant = 'icons',
  size = 'sm'
}: SocialShareButtonsProps) {
  const [copied, setCopied] = useState(false);
  const [showShareMenu, setShowShareMenu] = useState(false);

  // Get current page URL and title if not provided
  const shareUrl = url || (typeof window !== 'undefined' ? window.location.href : '');
  const shareTitle = title || (typeof document !== 'undefined' ? document.title : 'Check this out');

  const encodedUrl = encodeURIComponent(shareUrl);
  const encodedTitle = encodeURIComponent(shareTitle);
  const encodedDescription = encodeURIComponent(description);

  const sharePlatforms = [
    { 
      key: 'facebook', 
      name: 'Facebook',
      icon: Facebook, 
      shareUrl: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
      color: 'hover:bg-blue-500 hover:text-white hover:border-blue-500',
    },
    { 
      key: 'twitter', 
      name: 'Twitter',
      icon: Twitter, 
      shareUrl: `https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`,
      color: 'hover:bg-slate-800 hover:text-white hover:border-slate-800',
    },
    { 
      key: 'linkedin', 
      name: 'LinkedIn',
      icon: Linkedin, 
      shareUrl: `https://www.linkedin.com/shareArticle?mini=true&url=${encodedUrl}&title=${encodedTitle}&summary=${encodedDescription}`,
      color: 'hover:bg-blue-600 hover:text-white hover:border-blue-600',
    },
    { 
      key: 'whatsapp', 
      name: 'WhatsApp',
      icon: MessageCircle, 
      shareUrl: `https://wa.me/?text=${encodedTitle}%20${encodedUrl}`,
      color: 'hover:bg-green-500 hover:text-white hover:border-green-500',
    },
  ];

  const handleShare = (platform: typeof sharePlatforms[0]) => {
    window.open(platform.shareUrl, '_blank', 'width=600,height=400');
    setShowShareMenu(false);
  };

  const copyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(shareUrl);
      setCopied(true);
      toast.success('Link copied to clipboard!');
      setTimeout(() => setCopied(false), 2000);
    } catch {
      toast.error('Failed to copy link');
    }
  };

  const buttonSize = size === 'sm' ? 'w-8 h-8' : 'w-10 h-10';
  const iconSize = size === 'sm' ? 'w-4 h-4' : 'w-5 h-5';

  // Compact variant - shows a share button with dropdown
  if (variant === 'compact') {
    return (
      <div className={`relative ${className}`}>
        <Button
          variant="outline"
          size="sm"
          onClick={() => setShowShareMenu(!showShareMenu)}
          className="gap-2"
        >
          <Share2 className="w-4 h-4" />
          <span className="hidden sm:inline">Share</span>
        </Button>
        
        <AnimatePresence>
          {showShareMenu && (
            <>
              <div 
                className="fixed inset-0 z-40" 
                onClick={() => setShowShareMenu(false)}
              />
              <motion.div
                initial={{ opacity: 0, scale: 0.95, y: -10 }}
                animate={{ opacity: 1, scale: 1, y: 0 }}
                exit={{ opacity: 0, scale: 0.95, y: -10 }}
                transition={{ duration: 0.15 }}
                className="absolute right-0 top-full mt-2 bg-white dark:bg-slate-800 border rounded-lg shadow-lg p-2 z-50 min-w-[140px]"
              >
                {sharePlatforms.map((platform) => (
                  <button
                    key={platform.key}
                    onClick={() => handleShare(platform)}
                    className="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
                  >
                    <platform.icon className="w-4 h-4" />
                    {platform.name}
                  </button>
                ))}
                <button
                  onClick={copyToClipboard}
                  className="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-md hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors border-t mt-1 pt-2"
                >
                  {copied ? <Check className="w-4 h-4 text-green-500" /> : <Link2 className="w-4 h-4" />}
                  {copied ? 'Copied!' : 'Copy Link'}
                </button>
              </motion.div>
            </>
          )}
        </AnimatePresence>
      </div>
    );
  }

  // Buttons variant - shows text labels
  if (variant === 'buttons') {
    return (
      <div className={`flex items-center gap-2 flex-wrap ${className}`}>
        <span className="text-sm text-muted-foreground mr-1">Share:</span>
        {sharePlatforms.map((platform) => (
          <Button
            key={platform.key}
            variant="outline"
            size="sm"
            onClick={() => handleShare(platform)}
            className={`gap-1 ${platform.color}`}
          >
            <platform.icon className="w-4 h-4" />
            <span className="hidden sm:inline">{platform.name}</span>
          </Button>
        ))}
        <Button
          variant="outline"
          size="sm"
          onClick={copyToClipboard}
          className={`gap-1 ${copied ? 'bg-green-500 text-white border-green-500' : ''}`}
        >
          {copied ? <Check className="w-4 h-4" /> : <Link2 className="w-4 h-4" />}
          <span className="hidden sm:inline">{copied ? 'Copied!' : 'Copy Link'}</span>
        </Button>
      </div>
    );
  }

  // Icons variant - default, just icons
  return (
    <div className={`flex items-center gap-1.5 ${className}`}>
      <span className="text-xs text-muted-foreground mr-1">Share:</span>
      {sharePlatforms.map((platform) => (
        <motion.button
          key={platform.key}
          onClick={() => handleShare(platform)}
          title={`Share on ${platform.name}`}
          className={`
            flex items-center justify-center rounded-full
            bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400
            border border-transparent transition-all duration-200
            ${buttonSize}
            ${platform.color}
          `}
          whileHover={{ scale: 1.1 }}
          whileTap={{ scale: 0.95 }}
        >
          <platform.icon className={iconSize} />
        </motion.button>
      ))}
      <motion.button
        onClick={copyToClipboard}
        title="Copy link"
        className={`
          flex items-center justify-center rounded-full
          bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400
          border border-transparent transition-all duration-200
          ${buttonSize}
          ${copied ? 'bg-green-500 text-white border-green-500' : 'hover:bg-emerald-500 hover:text-white hover:border-emerald-500'}
        `}
        whileHover={{ scale: 1.1 }}
        whileTap={{ scale: 0.95 }}
      >
        {copied ? <Check className={iconSize} /> : <Link2 className={iconSize} />}
      </motion.button>
    </div>
  );
}

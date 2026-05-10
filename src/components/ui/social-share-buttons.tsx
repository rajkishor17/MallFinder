'use client';

import { useState } from 'react';
import { 
  Facebook, 
  Linkedin, 
  MessageCircle,
  Share2,
  Link2,
  Check
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
      name: 'X',
      icon: XIcon, 
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
    { 
      key: 'pinterest', 
      name: 'Pinterest',
      icon: PinterestIcon, 
      shareUrl: `https://pinterest.com/pin/create/button/?url=${encodedUrl}&description=${encodedTitle}`,
      color: 'hover:bg-red-600 hover:text-white hover:border-red-600',
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

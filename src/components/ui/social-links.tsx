'use client';

import { 
  Facebook, 
  Instagram, 
  Linkedin, 
  Youtube,
  MessageCircle
} from 'lucide-react';
import { motion } from 'framer-motion';

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

interface SocialLinksProps {
  links: {
    facebook?: string;
    twitter?: string;
    instagram?: string;
    linkedin?: string;
    youtube?: string;
    whatsapp?: string;
    pinterest?: string;
  };
  className?: string;
  iconSize?: 'sm' | 'md' | 'lg';
  showLabels?: boolean;
}

const iconSizeClasses = {
  sm: 'w-4 h-4',
  md: 'w-5 h-5',
  lg: 'w-6 h-6',
};

const buttonSizeClasses = {
  sm: 'w-8 h-8',
  md: 'w-10 h-10',
  lg: 'w-12 h-12',
};

export function SocialLinks({ 
  links, 
  className = '', 
  iconSize = 'md',
  showLabels = false 
}: SocialLinksProps) {
  const socialPlatforms = [
    { 
      key: 'facebook', 
      url: links.facebook, 
      icon: Facebook, 
      label: 'Facebook',
      hoverClass: 'hover:bg-blue-500 hover:text-white',
    },
    { 
      key: 'twitter', 
      url: links.twitter, 
      icon: XIcon, 
      label: 'X',
      hoverClass: 'hover:bg-slate-800 hover:text-white',
    },
    { 
      key: 'instagram', 
      url: links.instagram, 
      icon: Instagram, 
      label: 'Instagram',
      hoverClass: 'hover:bg-pink-500 hover:text-white',
    },
    { 
      key: 'linkedin', 
      url: links.linkedin, 
      icon: Linkedin, 
      label: 'LinkedIn',
      hoverClass: 'hover:bg-blue-600 hover:text-white',
    },
    { 
      key: 'youtube', 
      url: links.youtube, 
      icon: Youtube, 
      label: 'YouTube',
      hoverClass: 'hover:bg-red-500 hover:text-white',
    },
    { 
      key: 'whatsapp', 
      url: links.whatsapp ? `https://wa.me/${links.whatsapp.replace(/\D/g, '')}` : null, 
      icon: MessageCircle, 
      label: 'WhatsApp',
      hoverClass: 'hover:bg-green-500 hover:text-white',
    },
    { 
      key: 'pinterest', 
      url: links.pinterest, 
      icon: PinterestIcon, 
      label: 'Pinterest',
      hoverClass: 'hover:bg-red-600 hover:text-white',
    },
  ];

  const activePlatforms = socialPlatforms.filter(platform => platform.url);

  if (activePlatforms.length === 0) {
    return null;
  }

  return (
    <div className={`flex items-center gap-2 ${className}`}>
      {activePlatforms.map((platform) => (
        <motion.a
          key={platform.key}
          href={platform.url!}
          target="_blank"
          rel="noopener noreferrer"
          title={platform.label}
          className={`
            flex items-center justify-center rounded-full
            bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400
            transition-all duration-200
            ${buttonSizeClasses[iconSize]}
            ${platform.hoverClass}
          `}
          whileHover={{ scale: 1.1 }}
          whileTap={{ scale: 0.95 }}
        >
          <platform.icon className={iconSizeClasses[iconSize]} />
          {showLabels && (
            <span className="ml-2 text-sm">{platform.label}</span>
          )}
        </motion.a>
      ))}
    </div>
  );
}

// Skeleton loader for social links
export function SocialLinksSkeleton({ className = '' }: { className?: string }) {
  return (
    <div className={`flex items-center gap-2 ${className}`}>
      {[1, 2, 3, 4].map((i) => (
        <div 
          key={i} 
          className="w-10 h-10 rounded-full bg-slate-200 dark:bg-slate-700 animate-pulse"
        />
      ))}
    </div>
  );
}

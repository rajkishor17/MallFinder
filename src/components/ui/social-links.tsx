'use client';

import { 
  Facebook, 
  Twitter, 
  Instagram, 
  Linkedin, 
  Youtube,
  MessageCircle
} from 'lucide-react';
import { motion } from 'framer-motion';

interface SocialLinksProps {
  links: {
    facebook?: string;
    twitter?: string;
    instagram?: string;
    linkedin?: string;
    youtube?: string;
    whatsapp?: string;
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
      icon: Twitter, 
      label: 'Twitter',
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

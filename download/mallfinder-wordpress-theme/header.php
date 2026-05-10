<?php
/**
 * Header Template
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <div class="inner">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-branding">
                <?php $logo = mallfinder_get_logo(); ?>
                <?php if ($logo) : ?>
                    <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(mallfinder_get_site_name()); ?>" style="max-height: 48px; max-width: 48px;" />
                <?php else : ?>
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
                    </div>
                <?php endif; ?>
                <div>
                    <h1 class="site-title"><?php echo esc_html(mallfinder_get_site_name()); ?></h1>
                    <p class="site-description"><?php echo esc_html(mallfinder_get_site_tagline()); ?></p>
                </div>
            </a>
            
            <nav class="main-nav">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/about/')); ?>"><?php _e('About Us', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php _e('Contact Us', 'mallfinder'); ?></a>
                <?php mallfinder_social_icons_header(); ?>
            </nav>
            
            <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="<?php esc_attr_e('Toggle Menu', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
        
        <nav class="mobile-nav" id="mobile-nav">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'mallfinder'); ?></a>
            <a href="<?php echo esc_url(home_url('/about/')); ?>"><?php _e('About Us', 'mallfinder'); ?></a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php _e('Contact Us', 'mallfinder'); ?></a>
            <?php if (mallfinder_has_social_media()) : ?>
                <div class="mobile-social-icons" style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-slate-200);">
                    <?php mallfinder_social_icons_footer(); ?>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

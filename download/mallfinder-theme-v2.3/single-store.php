<?php
/**
 * Single Store Template
 *
 * @package MallFinder
 */

get_header();

// Sidebar ad settings
$left_sidebar_enabled = get_option('mallfinder_left_sidebar_enable');
$right_sidebar_enabled = get_option('mallfinder_right_sidebar_enable');
$left_ad_type = get_option('mallfinder_left_ad_type', 'image');
$right_ad_type = get_option('mallfinder_right_ad_type', 'image');
$left_ad_image = get_option('mallfinder_left_ad_image');
$left_ad_url = get_option('mallfinder_left_ad_url');
$left_ad_code = get_option('mallfinder_left_ad_code');
$right_ad_image = get_option('mallfinder_right_ad_image');
$right_ad_url = get_option('mallfinder_right_ad_url');
$right_ad_code = get_option('mallfinder_right_ad_code');

while (have_posts()) : the_post();
    $store_id = get_the_ID();
    $store_image = mallfinder_get_store_image($store_id);
    $store_category = get_post_meta($store_id, '_store_category', true);
    $store_floor = get_post_meta($store_id, '_store_floor', true);
    $store_unit = get_post_meta($store_id, '_store_unit', true);
    $store_phone = get_post_meta($store_id, '_store_phone', true);
    $store_website = get_post_meta($store_id, '_store_website', true);
    $store_hours = get_post_meta($store_id, '_store_hours', true);
    $store_status = get_post_meta($store_id, '_store_status', true);
    $mall_id = get_post_meta($store_id, '_store_mall_id', true);

    $status_class = '';
    $status_label = '';
    $status_badge_class = '';
    switch ($store_status) {
        case 'OPEN':
            $status_class = 'store-open';
            $status_label = __('Open', 'mallfinder');
            $status_badge_class = 'badge-open';
            break;
        case 'COMING_SOON':
            $status_class = 'store-coming-soon';
            $status_label = __('Coming Soon', 'mallfinder');
            $status_badge_class = 'badge-coming-soon';
            break;
        case 'CLOSED':
            $status_class = 'store-closed';
            $status_label = __('Closed', 'mallfinder');
            $status_badge_class = 'badge-closed';
            break;
        default:
            $status_class = 'store-open';
            $status_label = __('Open', 'mallfinder');
            $status_badge_class = 'badge-open';
    }
?>

<!-- Hero Section -->
<div class="mall-hero" style="height: 240px;">
    <?php if ($store_image) : ?>
        <div class="mall-hero-image">
            <img src="<?php echo esc_url($store_image); ?>" alt="<?php the_title_attribute(); ?>">
        </div>
    <?php else : ?>
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; color: rgba(255,255,255,0.2);">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </div>
    <?php endif; ?>

    <div class="mall-hero-content">
        <div class="container">
            <span class="badge <?php echo esc_attr($status_badge_class); ?>" style="margin-bottom: 0.5rem;"><?php echo esc_html($status_label); ?></span>
            <h1 style="font-size: 1.5rem;"><?php the_title(); ?></h1>
            <?php if ($store_category) : ?>
                <p style="margin: 0; color: rgba(255,255,255,0.8); font-size: 0.875rem;"><?php echo esc_html($store_category); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Social Share Buttons (Hero Overlay) -->
    <div class="hero-social-share">
        <div class="social-share-buttons">
            <button class="social-share-btn facebook" onclick="shareOnFacebook()" aria-label="Share on Facebook">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </button>
            <button class="social-share-btn twitter" onclick="shareOnTwitter()" aria-label="Share on Twitter">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </button>
            <button class="social-share-btn whatsapp" onclick="shareOnWhatsApp()" aria-label="Share on WhatsApp">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </button>
            <button class="social-share-btn copy-link" onclick="copyLink(this)" aria-label="Copy link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </button>
        </div>
    </div>
</div>

<!-- Main Content Wrapper with Sidebars -->
<div class="store-wrapper<?php echo $left_sidebar_enabled ? ' has-left-sidebar' : ''; ?><?php echo $right_sidebar_enabled ? ' has-right-sidebar' : ''; ?>">
    
    <!-- Left Sidebar -->
    <?php if ($left_sidebar_enabled) : ?>
    <aside class="sidebar sidebar-left">
        <div class="sidebar-ad">
            <?php if ($left_ad_type === 'image' && $left_ad_image) : ?>
                <?php if ($left_ad_url) : ?>
                    <a href="<?php echo esc_url($left_ad_url); ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url($left_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url($left_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                <?php endif; ?>
            <?php elseif ($left_ad_type === 'google' && $left_ad_code) : ?>
                <div class="sidebar-ad-code">
                    <?php echo $left_ad_code; ?>
                </div>
            <?php else : ?>
                <div class="sidebar-ad-placeholder">
                    <span><?php _e('Ad Space', 'mallfinder'); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </aside>
    <?php endif; ?>
    
    <!-- Main Content Area -->
    <main class="store-main-content">
        <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">

            <!-- Back to Mall -->
            <?php if ($mall_id) : ?>
                <div style="margin-bottom: 1.5rem;">
                    <a href="<?php echo esc_url(get_permalink($mall_id)); ?>" class="btn btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        <?php _e('Back to', 'mallfinder'); ?> <?php echo esc_html(get_the_title($mall_id)); ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="mall-content-grid">
                <!-- About Card -->
                <div class="info-card">
                    <h3><?php _e('About', 'mallfinder'); ?></h3>
                    <?php if (get_the_content()) : ?>
                        <div class="store-description" style="color: var(--slate-600); margin-bottom: 1rem; line-height: 1.7;">
                            <?php the_content(); ?>
                        </div>
                    <?php elseif (has_excerpt()) : ?>
                        <p style="color: var(--slate-600); margin-bottom: 1rem;"><?php the_excerpt(); ?></p>
                    <?php else : ?>
                        <p style="color: var(--slate-500); font-style: italic; margin-bottom: 1rem;"><?php _e('No description available for this store.', 'mallfinder'); ?></p>
                    <?php endif; ?>

                    <?php if ($store_floor || $store_unit) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Location', 'mallfinder'); ?></div>
                                <div class="info-item-value"><?php echo esc_html(trim($store_floor . ' - ' . $store_unit, ' -')); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($store_hours) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Opening Hours', 'mallfinder'); ?></div>
                                <div class="info-item-value"><?php echo esc_html($store_hours); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Contact Card -->
                <div class="info-card">
                    <h3><?php _e('Contact', 'mallfinder'); ?></h3>

                    <?php if ($store_phone) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Phone', 'mallfinder'); ?></div>
                                <div class="info-item-value">
                                    <a href="tel:<?php echo esc_attr($store_phone); ?>" style="color: var(--emerald-600);"><?php echo esc_html($store_phone); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($store_website) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Website', 'mallfinder'); ?></div>
                                <div class="info-item-value">
                                    <a href="<?php echo esc_url($store_website); ?>" target="_blank" rel="noopener" style="color: var(--emerald-600);"><?php echo esc_html(parse_url($store_website, PHP_URL_HOST) ?: $store_website); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($mall_id) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Located In', 'mallfinder'); ?></div>
                                <div class="info-item-value">
                                    <a href="<?php echo esc_url(get_permalink($mall_id)); ?>" style="color: var(--emerald-600);"><?php echo esc_html(get_the_title($mall_id)); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        <?php if ($store_phone) : ?>
                            <a href="tel:<?php echo esc_attr($store_phone); ?>" class="btn btn-primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <?php _e('Call Now', 'mallfinder'); ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($store_website) : ?>
                            <a href="<?php echo esc_url($store_website); ?>" target="_blank" rel="noopener" class="btn btn-outline">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                                <?php _e('Visit Website', 'mallfinder'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- End store-main-content -->
    
    <!-- Right Sidebar -->
    <?php if ($right_sidebar_enabled) : ?>
    <aside class="sidebar sidebar-right">
        <div class="sidebar-ad">
            <?php if ($right_ad_type === 'image' && $right_ad_image) : ?>
                <?php if ($right_ad_url) : ?>
                    <a href="<?php echo esc_url($right_ad_url); ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url($right_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url($right_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                <?php endif; ?>
            <?php elseif ($right_ad_type === 'google' && $right_ad_code) : ?>
                <div class="sidebar-ad-code">
                    <?php echo $right_ad_code; ?>
                </div>
            <?php else : ?>
                <div class="sidebar-ad-placeholder">
                    <span><?php _e('Ad Space', 'mallfinder'); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </aside>
    <?php endif; ?>
    
</div><!-- End store-wrapper -->

<?php
endwhile;
get_footer();

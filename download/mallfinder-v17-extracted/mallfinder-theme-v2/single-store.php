<?php
/**
 * Single Store Template
 *
 * @package MallFinder
 */

get_header();

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
</div>

<!-- Main Content -->
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

<?php
endwhile;
get_footer();

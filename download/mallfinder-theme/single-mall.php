<?php
/**
 * Single Mall Template
 *
 * @package MallFinder
 */

get_header();

while (have_posts()) : the_post();
    $mall_id = get_the_ID();
    $image = get_the_post_thumbnail_url($mall_id, 'full');
    
    $address = get_post_meta($mall_id, '_mall_address', true);
    $state_id = get_post_meta($mall_id, '_mall_state', true);
    $city_id = get_post_meta($mall_id, '_mall_city', true);
    $area_id = get_post_meta($mall_id, '_mall_area', true);
    $pincode = get_post_meta($mall_id, '_mall_pincode', true);
    $latitude = get_post_meta($mall_id, '_mall_latitude', true);
    $longitude = get_post_meta($mall_id, '_mall_longitude', true);
    $phone = get_post_meta($mall_id, '_mall_phone', true);
    $email = get_post_meta($mall_id, '_mall_email', true);
    $website = get_post_meta($mall_id, '_mall_website', true);
    $opening_hours = get_post_meta($mall_id, '_mall_opening_hours', true);
    $total_stores = get_post_meta($mall_id, '_mall_total_stores', true);
    $floors = get_post_meta($mall_id, '_mall_floors', true);
    $parking = get_post_meta($mall_id, '_mall_parking', true);
    $category = get_post_meta($mall_id, '_mall_category', true);
    $expected_date = get_post_meta($mall_id, '_mall_expected_opening_date', true);
    $is_upcoming = ($category === 'upcoming');
?>

<!-- Hero Section -->
<div class="mall-hero" style="background: linear-gradient(to right, #1e293b, #0f172a);">
    <?php if ($image) : ?>
        <div class="mall-hero-image" style="position: absolute; inset: 0; opacity: 0.5;">
            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
    <?php else : ?>
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 96px; height: 96px; color: rgba(255,255,255,0.2);">
                <path d="M3 21h18"></path>
                <path d="M5 21V7l8-4v18"></path>
                <path d="M19 21V11l-6-4"></path>
            </svg>
        </div>
    <?php endif; ?>
    
    <div class="mall-hero-content" style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem;">
        <div class="container">
            <span class="badge <?php echo $is_upcoming ? 'badge-upcoming' : 'badge-existing'; ?>" style="margin-bottom: 0.75rem;">
                <?php if ($is_upcoming) : ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><path d="M12 3l1.912 5.813L20 10l-6.088 1.187L12 17l-1.912-5.813L4 10l6.088-1.187L12 3z"></path></svg>
                    <?php _e('Upcoming Mall', 'mallfinder'); ?>
                <?php else : ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                    <?php _e('Existing Mall', 'mallfinder'); ?>
                <?php endif; ?>
            </span>
            <h1 style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem;"><?php the_title(); ?></h1>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; color: rgba(255,255,255,0.8); font-size: 0.875rem;">
                <span style="display: flex; align-items: center; gap: 0.25rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <?php echo esc_html(mallfinder_format_address($mall_id)); ?>
                </span>
                <?php if ($opening_hours && !$is_upcoming) : ?>
                    <span style="display: flex; align-items: center; gap: 0.25rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <?php echo esc_html($opening_hours); ?>
                    </span>
                <?php endif; ?>
                <?php if ($expected_date && $is_upcoming) : ?>
                    <span style="display: flex; align-items: center; gap: 0.25rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <?php _e('Opening:', 'mallfinder'); ?> <?php echo esc_html($expected_date); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <div style="display: flex; gap: 1.5rem;">
        <!-- Main Column -->
        <div style="flex: 1; min-width: 0;">
            <!-- Search Section -->
            <div class="search-box" style="margin-bottom: 1.5rem;">
                <form class="search-row" method="GET" action="<?php echo get_post_type_archive_link('mall'); ?>">
                    <div class="search-input-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" name="s" class="search-input" placeholder="<?php esc_attr_e('Search malls by name, state, city, area, or address...', 'mallfinder'); ?>">
                    </div>
                    <button type="submit" class="btn <?php echo $is_upcoming ? 'btn-secondary' : 'btn-primary'; ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <?php _e('Search', 'mallfinder'); ?>
                    </button>
                </form>
            </div>

            <!-- Mall Details & Map Grid -->
            <div class="mall-content-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <!-- About Card -->
                <div class="info-card">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;"><?php _e('About', 'mallfinder'); ?></h3>
                    <div style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.6;">
                        <?php the_content(); ?>
                    </div>
                </div>

                <!-- Contact Info Card -->
                <div class="info-card">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;"><?php _e('Contact Information', 'mallfinder'); ?></h3>
                    
                    <?php if ($phone) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Phone', 'mallfinder'); ?></div>
                                <div class="info-item-value"><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($email) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Email', 'mallfinder'); ?></div>
                                <div class="info-item-value"><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($website) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Website', 'mallfinder'); ?></div>
                                <div class="info-item-value"><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener"><?php echo esc_html(parse_url($website, PHP_URL_HOST)); ?></a></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($total_stores) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Total Stores', 'mallfinder'); ?></div>
                                <div class="info-item-value"><?php echo esc_html($total_stores); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($floors) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Floors', 'mallfinder'); ?></div>
                                <div class="info-item-value"><?php echo esc_html($floors); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($parking) : ?>
                        <div class="info-item">
                            <div class="info-item-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
                            </div>
                            <div class="info-item-content">
                                <div class="info-item-label"><?php _e('Parking', 'mallfinder'); ?></div>
                                <div class="info-item-value"><?php echo esc_html($parking); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Map Section -->
            <?php if ($latitude && $longitude) : ?>
                <div class="info-card" style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;"><?php _e('Location', 'mallfinder'); ?></h3>
                    <?php echo mallfinder_render_map(array(
                        'id'     => 'single-mall-map',
                        'height' => '300px',
                        'malls'  => array($mall_id),
                    )); ?>
                </div>
            <?php endif; ?>

            <!-- Stores Section -->
            <div class="info-card">
                <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">
                    <?php _e('Stores in this Mall', 'mallfinder'); ?>
                </h3>
                <?php
                $stores = get_posts(array(
                    'post_type'      => 'store',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'meta_key'       => '_store_mall',
                    'meta_value'     => $mall_id,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                ));
                
                if ($stores) :
                ?>
                    <div class="store-list">
                        <?php foreach ($stores as $store) :
                            $logo = mallfinder_get_store_logo($store->ID);
                            $floor = get_post_meta($store->ID, '_store_floor', true);
                            $unit = get_post_meta($store->ID, '_store_unit', true);
                            $categories = mallfinder_get_store_categories($store->ID);
                        ?>
                            <div class="store-item">
                                <div class="store-logo">
                                    <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($store->post_title); ?>">
                                </div>
                                <div class="store-info">
                                    <div class="store-name">
                                        <a href="<?php echo get_permalink($store->ID); ?>"><?php echo esc_html($store->post_title); ?></a>
                                    </div>
                                    <div class="store-category">
                                        <?php echo esc_html(implode(', ', $categories)); ?>
                                        <?php if ($floor || $unit) : ?>
                                            | <?php echo esc_html(trim($floor . ' - ' . $unit, ' -')); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p style="color: var(--text-secondary); font-size: 0.875rem; padding: 1rem; background: #f8fafc; border-radius: var(--radius-md); text-align: center;">
                        <?php _e('No stores listed yet.', 'mallfinder'); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar (hidden on small screens) -->
        <aside class="content-sidebar" style="display: none;">
            <?php if (is_active_sidebar('mall-sidebar')) : ?>
                <?php dynamic_sidebar('mall-sidebar'); ?>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php
endwhile;

get_footer();

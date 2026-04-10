<?php
/**
 * Single Mall Template
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
    $mall_id = get_the_ID();
    $image = get_the_post_thumbnail_url($mall_id, 'full');
    $category = get_post_meta($mall_id, '_mall_category', true);
    $is_upcoming = ($category === 'upcoming');
    $address = mallfinder_format_address($mall_id);
    $opening_hours = get_post_meta($mall_id, '_mall_opening_hours', true);
    $phone = get_post_meta($mall_id, '_mall_phone', true);
    $website = get_post_meta($mall_id, '_mall_website', true);
    $stores_count = mallfinder_get_mall_stores_count($mall_id);
    $lat = get_post_meta($mall_id, '_mall_latitude', true);
    $lng = get_post_meta($mall_id, '_mall_longitude', true);
    
    // === GET AMENITIES ===
    $amenities = get_post_meta($mall_id, '_mall_amenities', true);
    
    // Get total stores count for pagination
    $total_stores_args = array(
        'post_type'      => 'store',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_store_mall_id',
        'meta_value'     => $mall_id,
        'fields'         => 'ids',
    );
    $total_stores_query = get_posts($total_stores_args);
    $total_stores = count($total_stores_query);
    
    // Pagination for stores
    $stores_per_page = 12;
    $current_page = isset($_GET['store_page']) ? max(1, intval($_GET['store_page'])) : 1;
    $offset = ($current_page - 1) * $stores_per_page;
    $total_pages = ceil($total_stores / $stores_per_page);
    
    // Get stores for this mall with pagination
    $stores = get_posts(array(
        'post_type'      => 'store',
        'posts_per_page' => $stores_per_page,
        'offset'         => $offset,
        'post_status'    => 'publish',
        'meta_key'       => '_store_mall_id',
        'meta_value'     => $mall_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    
    // Get unique store categories and statuses for filters
    $store_categories = array();
    $store_statuses = array();
    $all_stores_for_filters = get_posts(array(
        'post_type'      => 'store',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_store_mall_id',
        'meta_value'     => $mall_id,
    ));
    foreach ($all_stores_for_filters as $store) {
        $store_category = get_post_meta($store->ID, '_store_category', true);
        $store_status = get_post_meta($store->ID, '_store_status', true);
        if ($store_category && !in_array($store_category, $store_categories)) {
            $store_categories[] = $store_category;
        }
        if ($store_status && !in_array($store_status, $store_statuses)) {
            $store_statuses[] = $store_status;
        }
    }
    sort($store_categories);
?>

<!-- Hero Section -->
<div class="mall-hero">
    <?php if ($image) : ?>
        <div class="mall-hero-image">
            <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>">
        </div>
    <?php else : ?>
        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 96px; height: 96px; color: rgba(255,255,255,0.2);">
                <path d="M3 21h18"></path>
                <path d="M5 21V7l8-4v18"></path>
                <path d="M19 21V11l-6-4"></path>
            </svg>
        </div>
    <?php endif; ?>
    
    <div class="mall-hero-content">
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
            <h1><?php the_title(); ?></h1>
            <div class="mall-hero-meta">
                <div class="mall-hero-meta-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span><?php echo esc_html($address); ?></span>
                </div>
                <?php if ($opening_hours) : ?>
                    <div class="mall-hero-meta-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span><?php echo esc_html($opening_hours); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="page-wrapper">
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
    <main class="page-main-content">
    <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    
    <!-- Mall Search Box -->
    <div class="search-box">
        <form class="search-form" method="GET" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="search-main-row">
                <div class="search-input-wrapper" style="position: relative;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" name="s" id="mall-search-input-single" class="search-input" placeholder="<?php esc_attr_e('Search malls by name, state, city, area, or address...', 'mallfinder'); ?>" autocomplete="off">
                    <div id="search-suggestions-single" class="search-suggestions"></div>
                </div>
                <input type="hidden" name="post_type" value="mall">
                <button type="submit" class="btn btn-primary" id="search-btn-single">
                    <?php _e('Search', 'mallfinder'); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Mall Details & Map -->
    <div class="mall-content-grid">
        <!-- About Card -->
        <div class="info-card">
            <h3><?php _e('About', 'mallfinder'); ?></h3>
            <?php if (get_the_content()) : ?>
                <div class="mall-description" style="color: var(--slate-600); margin-bottom: 1rem; line-height: 1.7;">
                    <?php the_content(); ?>
                </div>
            <?php elseif (has_excerpt()) : ?>
                <p style="color: var(--slate-600); margin-bottom: 1rem;"><?php echo wp_trim_words(get_the_excerpt(), 50); ?></p>
            <?php else : ?>
                <p style="color: var(--slate-500); font-style: italic; margin-bottom: 1rem;"><?php _e('No description available for this mall.', 'mallfinder'); ?></p>
            <?php endif; ?>
            
            <div class="info-item">
                <div class="info-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div class="info-item-content">
                    <div class="info-item-label"><?php _e('Address', 'mallfinder'); ?></div>
                    <div class="info-item-value"><?php echo esc_html($address); ?></div>
                </div>
            </div>
            
            <?php if ($opening_hours) : ?>
                <div class="info-item">
                    <div class="info-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <div class="info-item-content">
                        <div class="info-item-label"><?php _e('Opening Hours', 'mallfinder'); ?></div>
                        <div class="info-item-value"><?php echo esc_html($opening_hours); ?></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($phone) : ?>
                <div class="info-item">
                    <div class="info-item-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <div class="info-item-content">
                        <div class="info-item-label"><?php _e('Phone', 'mallfinder'); ?></div>
                        <div class="info-item-value">
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" style="color: var(--emerald-600);"><?php echo esc_html($phone); ?></a>
                        </div>
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
                        <div class="info-item-value">
                            <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener" style="color: var(--emerald-600);"><?php echo esc_html(parse_url($website, PHP_URL_HOST) ?: $website); ?></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="info-item">
                <div class="info-item-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                </div>
                <div class="info-item-content">
                    <div class="info-item-label"><?php _e('Total Stores', 'mallfinder'); ?></div>
                    <div class="info-item-value"><?php echo esc_html($stores_count); ?> <?php _e('stores', 'mallfinder'); ?></div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                <?php if ($phone) : ?>
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <?php _e('Call Now', 'mallfinder'); ?>
                    </a>
                <?php endif; ?>
                <?php if ($website) : ?>
                    <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener" class="btn btn-outline">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                        <?php _e('Visit Website', 'mallfinder'); ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Social Share Buttons -->
            <div style="margin-top: 1.5rem;">
                <?php echo mallfinder_social_share_buttons(get_the_title() . ' - ' . get_bloginfo('name'), get_permalink(), 'compact'); ?>
            </div>
        </div>
        
        <!-- Map Card -->
        <div class="info-card">
            <h3><?php _e('Location', 'mallfinder'); ?></h3>
            <div class="map-container" style="height: 300px;">
                <div id="mall-single-map" style="width: 100%; height: 100%;"></div>
            </div>
            <?php if (!$lat || !$lng) : ?>
                <p style="color: var(--slate-500); font-size: 0.875rem; margin-top: 0.75rem; text-align: center;">
                    <?php _e('Location coordinates not available for this mall.', 'mallfinder'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- === FEATURES & AMENITIES SECTION === -->
    <?php if (!empty($amenities) && is_array($amenities)) : ?>
    <div class="amenities-section">
        <h3 class="amenities-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 20px; height: 20px;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
            <?php _e('Features & Amenities', 'mallfinder'); ?>
        </h3>
        <div class="amenities-grid">
            <?php 
            $amenity_labels = array(
                'parking' => __('Parking', 'mallfinder'),
                'free_parking' => __('Free Parking', 'mallfinder'),
                'valet_parking' => __('Valet Parking', 'mallfinder'),
                'wifi' => __('Free WiFi', 'mallfinder'),
                'food_court' => __('Food Court', 'mallfinder'),
                'restaurant' => __('Restaurant', 'mallfinder'),
                'cafe' => __('Cafe', 'mallfinder'),
                'movie_theater' => __('Cinema', 'mallfinder'),
                'gaming_zone' => __('Gaming Zone', 'mallfinder'),
                'kids_play_area' => __('Kids Play Area', 'mallfinder'),
                'restrooms' => __('Restrooms', 'mallfinder'),
                'wheelchair_access' => __('Wheelchair Access', 'mallfinder'),
                'atm' => __('ATM', 'mallfinder'),
                'prayer_room' => __('Prayer Room', 'mallfinder'),
                'nursing_room' => __('Nursing Room', 'mallfinder'),
                'locker' => __('Lockers', 'mallfinder'),
                'pet_friendly' => __('Pet Friendly', 'mallfinder'),
                'outdoor_seating' => __('Outdoor Seating', 'mallfinder'),
                'live_events' => __('Events Space', 'mallfinder'),
                'ev_charging' => __('EV Charging', 'mallfinder'),
            );
            
            $amenity_icons = array(
                'parking' => '<path d="M13 3H6v18h4v-6h3c3.31 0 6-2.69 6-6s-2.69-6-6-6zm.25 8H10V7h3.25c1.1 0 2 .9 2 2s-.9 2-2 2z"/>',
                'free_parking' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>',
                'valet_parking' => '<path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>',
                'wifi' => '<path d="M1 9l2 2c4.97-4.97 13.03-4.97 18 0l2-2C16.93 2.93 7.08 2.93 1 9zm8 8l3 3 3-3c-1.65-1.66-4.34-1.66-6 0zm-4-4l2 2c2.76-2.76 7.24-2.76 10 0l2-2C15.14 9.14 8.87 9.14 5 13z"/>',
                'food_court' => '<path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>',
                'restaurant' => '<path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/>',
                'cafe' => '<path d="M18.5 3H6c-1.1 0-2 .9-2 2v5.71c0 3.83 2.95 7.18 6.78 7.29 3.96.12 7.22-3.06 7.22-7v-1h.5c1.93 0 3.5-1.57 3.5-3.5S20.43 3 18.5 3zM16 5v3h-2V5h2zm-4 0v3H8V5h4zM6 5v3h-.01L6 5zm12.5 4H18V8h.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5zM6 19h12v2H6z"/>',
                'movie_theater' => '<path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/>',
                'gaming_zone' => '<path d="M21.58 16.09l-1.09-7.66C20.21 6.46 18.52 5 16.53 5H7.47C5.48 5 3.79 6.46 3.51 8.43l-1.09 7.66C2.2 17.63 3.39 19 4.94 19h.12c.68 0 1.32-.27 1.8-.75L9 16h6l2.25 2.25c.48.48 1.13.75 1.8.75h.12c1.56 0 2.75-1.37 2.53-2.91zM11 11H9v2H8v-2H6v-1h2V8h1v2h2v1zm4-1c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm2 3c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/>',
                'kids_play_area' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-6c.78 2.34 2.72 4 5 4s4.22-1.66 5-4H7zm8-4c.55 0 1-.45 1-1s-.45-1-1-1-1 .45-1 1 .45 1 1 1zm-6 0c.55 0 1-.45 1-1s-.45-1-1-1-1 .45-1 1 .45 1 1 1z"/>',
                'restrooms' => '<path d="M7 7c0-1.1-.9-2-2-2s-2 .9-2 2 .9 2 2 2 2-.9 2-2zm12.02 6.52l-2.6-5.22C16.12 7.69 15.09 7 13.99 7c-1.12 0-2.15.69-2.47 1.73l-.87 2.61c-.14.41-.53.7-.97.7-.32 0-.62-.15-.81-.41l-.73-.97C8.75 10.25 8.4 10 8 10c-.56 0-1 .44-1 1 0 .23.08.45.22.62l.73.97c.57.76 1.46 1.21 2.4 1.21.98 0 1.85-.49 2.35-1.26l1.39-2.03L15.28 16H14c-.55 0-1 .45-1 1v4c0 .55.45 1 1 1s1-.45 1-1v-3h2.33c.73 0 1.4-.4 1.74-1.04.28-.54.27-1.17-.05-1.44z"/>',
                'wheelchair_access' => '<path d="M12 2c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm4.78 11.58l-1.55-1.36c-.72-.63-1.18-1.45-1.28-2.35L14 9h-4l.05 2.87c-.1.9-.56 1.72-1.28 2.35l-1.55 1.36c-.17.15-.27.36-.27.59v2.33c0 .28.22.5.5.5h2v2c0 .55.45 1 1 1h3c.55 0 1-.45 1-1v-2h2c.28 0 .5-.22.5-.5v-2.33c0-.23-.1-.44-.27-.59z"/>',
                'atm' => '<path d="M8 9v1.5h2.25V15h1.5v-4.5H14V9zM6 9H3c-.55 0-1 .45-1 1v5h1.5v-1.5h2V15H7v-5c0-.55-.45-1-1-1zm-.5 3h-2v-1.5h2V12zM21 9h-4.5c-.55 0-1 .45-1 1v5H17v-4.5h1V14h1.5v-3.51h1V15H22v-5c0-.55-.45-1-1-1z"/>',
                'prayer_room' => '<path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z"/>',
                'nursing_room' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-2-3.5l4-2.5-4-2.5v5z"/>',
                'locker' => '<path d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM8 20H4V4h4v16zm6 0h-4V4h4v16zm6 0h-4V4h4v16z"/>',
                'pet_friendly' => '<circle cx="4.5" cy="9.5" r="2.5"/><circle cx="9" cy="5.5" r="2.5"/><circle cx="15" cy="5.5" r="2.5"/><circle cx="19.5" cy="9.5" r="2.5"/><path d="M17.34 14.86c-.87-1.02-1.6-1.89-2.48-2.91-.46-.54-1.05-1.08-1.75-1.32-.11-.04-.22-.07-.33-.09-.25-.04-.52-.04-.78-.04s-.53 0-.79.05c-.11.02-.22.05-.33.09-.7.24-1.28.78-1.75 1.32-.87 1.02-1.6 1.89-2.48 2.91-1.31 1.54-2.92 3.24-2.62 5.47.3 1.12 1.06 2.23 2.42 2.55.95.22 2.01-.11 2.83-.69.26-.19.55-.41.87-.54.52-.22 1.01-.22 1.53 0 .32.13.61.35.87.54.82.58 1.88.91 2.83.69 1.36-.32 2.12-1.43 2.42-2.55.3-2.23-1.31-3.93-2.62-5.47z"/>',
                'outdoor_seating' => '<path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>',
                'live_events' => '<path d="M7 14c1.66 0 3-1.34 3-3S8.66 8 7 8s-3 1.34-3 3 1.34 3 3 3zm0-4c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm12-3h-8v8H3V5H1v15h2v-3h18v3h2v-9c0-2.21-1.79-4-4-4z"/>',
                'ev_charging' => '<path d="M14.5 11l-3 6v-4h-2l3-6v4h2zM17 1H7c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 18H7V3h10v16z"/>',
            );
            
            foreach ($amenities as $amenity) : ?>
                <?php if (isset($amenity_labels[$amenity])) : ?>
                    <div class="amenity-item">
                        <div class="amenity-icon">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <?php echo isset($amenity_icons[$amenity]) ? $amenity_icons[$amenity] : '<circle cx="12" cy="12" r="8"/>'; ?>
                            </svg>
                        </div>
                        <span class="amenity-label"><?php echo esc_html($amenity_labels[$amenity]); ?></span>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <!-- === END FEATURES & AMENITIES SECTION === -->

    <!-- Stores Section -->
    <?php if ($total_stores > 0) : ?>
        <section class="section" id="stores-section">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php _e('Stores in Mall', 'mallfinder'); ?></h2>
                    <p class="section-subtitle">
                        <?php printf(_n('%s store', '%s stores', $total_stores, 'mallfinder'), number_format_i18n($total_stores)); ?>
                    </p>
                </div>
            </div>
            
            <!-- Store Search Box -->
            <div class="store-search-box">
                <div class="store-search-row">
                    <div class="store-search-input-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" id="store-search-input" class="store-search-input" placeholder="<?php esc_attr_e('Search stores by name...', 'mallfinder'); ?>">
                    </div>
                    <?php if (!empty($store_categories)) : ?>
                    <select id="store-category-filter" class="store-filter-select">
                        <option value=""><?php _e('All Categories', 'mallfinder'); ?></option>
                        <?php foreach ($store_categories as $cat) : ?>
                            <option value="<?php echo esc_attr($cat); ?>"><?php echo esc_html($cat); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                    <select id="store-status-filter" class="store-filter-select">
                        <option value=""><?php _e('All Status', 'mallfinder'); ?></option>
                        <option value="OPEN"><?php _e('Open', 'mallfinder'); ?></option>
                        <option value="COMING_SOON"><?php _e('Coming Soon', 'mallfinder'); ?></option>
                        <option value="CLOSED"><?php _e('Closed', 'mallfinder'); ?></option>
                    </select>
                    <button type="button" class="btn btn-primary" id="store-search-btn">
                        <?php _e('Search', 'mallfinder'); ?>
                    </button>
                    <button type="button" class="btn btn-ghost" id="store-clear-btn">
                        <?php _e('Clear', 'mallfinder'); ?>
                    </button>
                </div>
            </div>

            <div class="store-list" id="store-list">
                <?php foreach ($stores as $store) : ?>
                    <?php
                    $store_id = $store->ID;
                    $store_image = mallfinder_get_store_image($store_id);
                    $store_category = get_post_meta($store_id, '_store_category', true);
                    $store_floor = get_post_meta($store_id, '_store_floor', true);
                    $store_unit = get_post_meta($store_id, '_store_unit', true);
                    $store_status = get_post_meta($store_id, '_store_status', true);
                    
                    $status_class = '';
                    $status_label = '';
                    switch ($store_status) {
                        case 'OPEN':
                            $status_class = 'badge-open';
                            $status_label = __('Open', 'mallfinder');
                            break;
                        case 'COMING_SOON':
                            $status_class = 'badge-coming-soon';
                            $status_label = __('Coming Soon', 'mallfinder');
                            break;
                        case 'CLOSED':
                            $status_class = 'badge-closed';
                            $status_label = __('Closed', 'mallfinder');
                            break;
                        default:
                            $status_class = 'badge-open';
                            $status_label = __('Open', 'mallfinder');
                    }
                    ?>
                    <article class="store-card <?php echo $store_status === 'CLOSED' ? 'closed' : ''; ?>" 
                             data-name="<?php echo esc_attr(strtolower($store->post_title)); ?>" 
                             data-category="<?php echo esc_attr($store_category); ?>" 
                             data-status="<?php echo esc_attr($store_status); ?>">
                        <div class="store-card-image">
                            <img src="<?php echo esc_url($store_image); ?>" alt="<?php echo esc_attr($store->post_title); ?>">
                        </div>
                        <div class="store-card-body">
                            <div class="store-card-header">
                                <div>
                                    <h3 class="store-card-title"><?php echo esc_html($store->post_title); ?></h3>
                                    <p class="store-card-category"><?php echo esc_html($store_category); ?></p>
                                </div>
                                <span class="badge <?php echo esc_attr($status_class); ?>"><?php echo esc_html($status_label); ?></span>
                            </div>
                            <?php if ($store_floor || $store_unit) : ?>
                                <div class="store-card-location">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    <span><?php echo esc_html(trim($store_floor . ' - ' . $store_unit, ' -')); ?></span>
                                </div>
                            <?php endif; ?>
                            <a href="<?php echo esc_url(get_permalink($store_id)); ?>" class="btn btn-sm btn-primary" style="width: 100%; margin-top: 0.75rem;">
                                <?php _e('View Details', 'mallfinder'); ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($total_pages > 1) : ?>
            <!-- Stores Pagination -->
            <div class="pagination" id="stores-pagination">
                <?php
                echo paginate_links(array(
                    'total'     => $total_stores,
                    'current'   => $current_page,
                    'per_page' => $stores_per_page,
                    'prev_text' => __('&laquo; Previous', 'mallfinder'),
                    'next_text' => __('Next &raquo;', 'mallfinder'),
                    'format'    => '?store_page=%#%',
                    'add_args'  => array('#stores-section'),
                ));
                ?>
            </div>
            <?php endif; ?>
            
            <div id="no-stores-message" style="display: none; text-align: center; padding: 2rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 48px; height: 48px; color: var(--slate-400); margin: 0 auto 1rem;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <p style="color: var(--slate-500);"><?php _e('No stores found matching your criteria.', 'mallfinder'); ?></p>
            </div>
        </section>
    <?php else : ?>
        <section class="section">
            <div class="section-header">
                <div>
                    <h2 class="section-title"><?php _e('Stores in Mall', 'mallfinder'); ?></h2>
                    <p class="section-subtitle"><?php _e('No stores listed yet', 'mallfinder'); ?></p>
                </div>
            </div>
        </section>
    <?php endif; ?>
    </div><!-- End container -->
    </main><!-- End page-main-content -->
    
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
</div><!-- End page-wrapper -->

<!-- Map Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($lat && $lng) : ?>
    var lat = parseFloat('<?php echo esc_js($lat); ?>');
    var lng = parseFloat('<?php echo esc_js($lng); ?>');
    
    // Check if Leaflet is loaded
    if (typeof L !== 'undefined') {
        // Initialize the map
        var mapContainer = document.getElementById('mall-single-map');
        if (mapContainer) {
            var map = L.map('mall-single-map', {
                center: [lat, lng],
                zoom: 15
            });
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            
            // Create custom marker icon
            var markerColor = '<?php echo $is_upcoming ? '#f59e0b' : '#10b981'; ?>';
            var customIcon = L.divIcon({
                html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="' + markerColor + '" width="32" height="32"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>',
                className: 'custom-marker-icon',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });
            
            // Add marker with popup
            L.marker([lat, lng], { icon: customIcon }).addTo(map)
                .bindPopup('<strong><?php echo esc_js(get_the_title()); ?></strong><br><?php echo esc_js($address); ?>');
            
            // Force map to redraw after a short delay
            setTimeout(function() {
                map.invalidateSize();
            }, 100);
        }
    } else {
        console.log('Leaflet library not loaded');
    }
    <?php endif; ?>
});
</script>

<?php
// Get other malls for "Explore Other Malls" section
$other_malls = get_posts(array(
    'post_type'      => 'mall',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
    'post__not_in'   => array($mall_id),
    'orderby'        => 'date',
    'order'          => 'DESC',
));
?>

<!-- Explore Other Malls Section -->
<?php if (!empty($other_malls)) : ?>
<section class="explore-malls-section">
    <div class="container">
        <div class="explore-section-header">
            <h2 class="explore-section-title"><?php _e('Explore Other Malls', 'mallfinder'); ?></h2>
            <p class="explore-section-subtitle"><?php _e('Discover more shopping destinations near you', 'mallfinder'); ?></p>
        </div>
        
        <div class="explore-malls-carousel-wrapper">
            <button class="carousel-btn carousel-btn-prev" id="single-carousel-prev" aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>
            
            <div class="explore-malls-carousel" id="explore-malls-carousel">
                <?php foreach ($other_malls as $other_mall) : ?>
                    <?php
                    $other_image = mallfinder_get_mall_image($other_mall->ID);
                    $other_category = get_post_meta($other_mall->ID, '_mall_category', true);
                    $other_is_upcoming = ($other_category === 'upcoming');
                    $other_address = mallfinder_format_address($other_mall->ID);
                    ?>
                    <a href="<?php echo esc_url(get_permalink($other_mall->ID)); ?>" class="explore-mall-card <?php echo $other_is_upcoming ? 'upcoming' : ''; ?>">
                        <div class="explore-mall-image">
                            <?php if ($other_image) : ?>
                                <img src="<?php echo esc_url($other_image); ?>" alt="<?php echo esc_attr($other_mall->post_title); ?>">
                            <?php else : ?>
                                <div class="explore-mall-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 21h18"></path>
                                        <path d="M5 21V7l8-4v18"></path>
                                        <path d="M19 21V11l-6-4"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <span class="explore-mall-badge <?php echo $other_is_upcoming ? 'badge-upcoming' : 'badge-existing'; ?>">
                                <?php echo $other_is_upcoming ? __('Upcoming', 'mallfinder') : __('Existing', 'mallfinder'); ?>
                            </span>
                        </div>
                        <div class="explore-mall-content">
                            <h3><?php echo esc_html($other_mall->post_title); ?></h3>
                            <p><?php echo esc_html(wp_trim_words($other_address, 8)); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <button class="carousel-btn carousel-btn-next" id="single-carousel-next" aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>
        
        <div class="explore-section-footer">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php _e('View All Malls', 'mallfinder'); ?></a>
        </div>
    </div>
</section>

<script>
(function() {
    var carousel = document.getElementById('explore-malls-carousel');
    var prevBtn = document.getElementById('single-carousel-prev');
    var nextBtn = document.getElementById('single-carousel-next');
    
    if (carousel && prevBtn && nextBtn) {
        var scrollAmount = 300;
        
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });
        
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }
})();
</script>
<?php endif; ?>

<!-- Search Autocomplete Script for Single Mall Page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('mall-search-input-single');
    var suggestionsBox = document.getElementById('search-suggestions-single');
    var searchTimeout = null;
    
    if (searchInput && suggestionsBox) {
        searchInput.addEventListener('input', function() {
            var searchTerm = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (searchTerm.length < 2) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
                return;
            }
            
            searchTimeout = setTimeout(function() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=mallfinder_search&term=' + encodeURIComponent(searchTerm) + '&category=all&nonce=<?php echo wp_create_nonce('mallfinder_nonce'); ?>', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            var results = JSON.parse(xhr.responseText);
                            if (results.length > 0) {
                                var html = '';
                                results.forEach(function(mall) {
                                    var badgeClass = mall.category === 'upcoming' ? 'badge-upcoming' : 'badge-existing';
                                    var badgeText = mall.category === 'upcoming' ? 'Upcoming' : 'Existing';
                                    html += '<a href="' + mall.url + '" class="suggestion-item">';
                                    html += '<img src="' + mall.image + '" alt="' + mall.title + '" class="suggestion-image">';
                                    html += '<div class="suggestion-content">';
                                    html += '<div class="suggestion-title">' + mall.title + '</div>';
                                    html += '<div class="suggestion-address">' + mall.address + '</div>';
                                    html += '</div>';
                                    html += '<span class="suggestion-badge ' + badgeClass + '">' + badgeText + '</span>';
                                    html += '</a>';
                                });
                                suggestionsBox.innerHTML = html;
                                suggestionsBox.style.display = 'block';
                            } else {
                                suggestionsBox.innerHTML = '<div class="suggestion-empty">No malls found</div>';
                                suggestionsBox.style.display = 'block';
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                        }
                    }
                };
                xhr.send();
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });
        
        // Show suggestions on focus if there's content
        searchInput.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                this.dispatchEvent(new Event('input'));
            }
        });
    }
});
</script>

<!-- Store Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var storeSearchInput = document.getElementById('store-search-input');
    var storeCategoryFilter = document.getElementById('store-category-filter');
    var storeStatusFilter = document.getElementById('store-status-filter');
    var storeSearchBtn = document.getElementById('store-search-btn');
    var storeClearBtn = document.getElementById('store-clear-btn');
    var storeList = document.getElementById('store-list');
    var noStoresMessage = document.getElementById('no-stores-message');
    
    if (storeSearchInput && storeList) {
        function filterStores() {
            var searchTerm = storeSearchInput.value.toLowerCase().trim();
            var categoryValue = storeCategoryFilter ? storeCategoryFilter.value : '';
            var statusValue = storeStatusFilter ? storeStatusFilter.value : '';
            
            var storeCards = storeList.querySelectorAll('.store-card');
            var visibleCount = 0;
            
            storeCards.forEach(function(card) {
                var name = card.getAttribute('data-name') || '';
                var category = card.getAttribute('data-category') || '';
                var status = card.getAttribute('data-status') || '';
                
                var matchesSearch = !searchTerm || name.indexOf(searchTerm) !== -1;
                var matchesCategory = !categoryValue || category === categoryValue;
                var matchesStatus = !statusValue || status === statusValue;
                
                if (matchesSearch && matchesCategory && matchesStatus) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (noStoresMessage) {
                noStoresMessage.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
        
        if (storeSearchBtn) {
            storeSearchBtn.addEventListener('click', function() {
                filterStores();
            });
        }
        
        if (storeClearBtn) {
            storeClearBtn.addEventListener('click', function() {
                storeSearchInput.value = '';
                if (storeCategoryFilter) storeCategoryFilter.value = '';
                if (storeStatusFilter) storeStatusFilter.value = '';
                filterStores();
            });
        }
        
        // Also filter on Enter key
        storeSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                filterStores();
            }
        });
        
        // Filter on select change
        if (storeCategoryFilter) {
            storeCategoryFilter.addEventListener('change', filterStores);
        }
        if (storeStatusFilter) {
            storeStatusFilter.addEventListener('change', filterStores);
        }
    }
});
</script>

<?php
endwhile;
get_footer();

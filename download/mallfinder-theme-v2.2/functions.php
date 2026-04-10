<?php
/**
 * MallFinder Theme Functions
 *
 * @package MallFinder
 * @version 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme version
define('MALLFINDER_VERSION', '1.0.0');

/**
 * Theme Setup
 */
function mallfinder_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Image sizes
    add_image_size('mall-thumbnail', 400, 300, true);
    add_image_size('mall-hero', 1920, 600, true);
    add_image_size('store-logo', 200, 200, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mallfinder'),
        'footer'  => __('Footer Menu', 'mallfinder'),
    ));
}
add_action('after_setup_theme', 'mallfinder_setup');

/**
 * Enqueue Scripts and Styles
 */
function mallfinder_scripts() {
    // Styles
    wp_enqueue_style('mallfinder-style', get_stylesheet_uri(), array(), MALLFINDER_VERSION);
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');

    // Scripts
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
    wp_enqueue_script('mallfinder-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery', 'leaflet-js'), MALLFINDER_VERSION, true);

    // Localize script
    wp_localize_script('mallfinder-main', 'mallfinderData', array(
        'ajaxUrl'     => admin_url('admin-ajax.php'),
        'nonce'       => wp_create_nonce('mallfinder_nonce'),
        'defaultLat'  => get_option('mallfinder_default_lat', '40.7128'),
        'defaultLng'  => get_option('mallfinder_default_lng', '-74.0060'),
        'defaultZoom' => get_option('mallfinder_default_zoom', '12'),
    ));
}
add_action('wp_enqueue_scripts', 'mallfinder_scripts');

/**
 * Register Mall Post Type
 */
function mallfinder_register_mall_post_type() {
    $labels = array(
        'name'               => __('Malls', 'mallfinder'),
        'singular_name'      => __('Mall', 'mallfinder'),
        'menu_name'          => __('Malls', 'mallfinder'),
        'all_items'          => __('All Malls', 'mallfinder'),
        'add_new_item'       => __('Add New Mall', 'mallfinder'),
        'edit_item'          => __('Edit Mall', 'mallfinder'),
        'featured_image'     => __('Mall Image', 'mallfinder'),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-store',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'             => array('slug' => 'malls'),
        'show_in_rest'        => true,
    );

    register_post_type('mall', $args);
}
add_action('init', 'mallfinder_register_mall_post_type');

/**
 * Register Store Post Type
 */
function mallfinder_register_store_post_type() {
    $labels = array(
        'name'               => __('Stores', 'mallfinder'),
        'singular_name'      => __('Store', 'mallfinder'),
        'menu_name'          => __('Stores', 'mallfinder'),
        'all_items'          => __('All Stores', 'mallfinder'),
        'add_new_item'       => __('Add New Store', 'mallfinder'),
        'edit_item'          => __('Edit Store', 'mallfinder'),
        'featured_image'     => __('Store Logo', 'mallfinder'),
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-cart',
        'supports'            => array('title', 'editor', 'thumbnail'),
        'rewrite'             => array('slug' => 'stores'),
        'show_in_rest'        => true,
    );

    register_post_type('store', $args);
}
add_action('init', 'mallfinder_register_store_post_type');

/**
 * Add Meta Box for Mall Details
 */
function mallfinder_add_mall_meta_box() {
    add_meta_box(
        'mall_details',
        __('Mall Details', 'mallfinder'),
        'mallfinder_mall_meta_box_callback',
        'mall',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mallfinder_add_mall_meta_box');

function mallfinder_mall_meta_box_callback($post) {
    wp_nonce_field('mallfinder_mall_meta', 'mallfinder_mall_meta_nonce');
    
    $fields = array(
        'address' => __('Address', 'mallfinder'),
        'area' => __('Area', 'mallfinder'),
        'city' => __('City', 'mallfinder'),
        'state' => __('State', 'mallfinder'),
        'pincode' => __('PIN Code', 'mallfinder'),
        'latitude' => __('Latitude', 'mallfinder'),
        'longitude' => __('Longitude', 'mallfinder'),
        'phone' => __('Phone', 'mallfinder'),
        'website' => __('Website URL', 'mallfinder'),
        'opening_hours' => __('Opening Hours', 'mallfinder'),
        'total_stores' => __('Total Stores', 'mallfinder'),
        'category' => __('Category', 'mallfinder'),
    );
    
    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, '_mall_' . $key, true);
        echo '<tr>';
        echo '<th><label for="mall_' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
        echo '<td>';
        if ($key === 'category') {
            echo '<select name="mall_' . esc_attr($key) . '" id="mall_' . esc_attr($key) . '">';
            echo '<option value="existing"' . selected($value, 'existing', false) . '>' . __('Existing', 'mallfinder') . '</option>';
            echo '<option value="upcoming"' . selected($value, 'upcoming', false) . '>' . __('Upcoming', 'mallfinder') . '</option>';
            echo '</select>';
        } else {
            echo '<input type="text" name="mall_' . esc_attr($key) . '" id="mall_' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text">';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    // === FEATURES & AMENITIES SECTION ===
    echo '<h3 style="margin-top: 20px; padding-top: 10px; border-top: 1px solid #ccc;">' . __('Features & Amenities', 'mallfinder') . '</h3>';
    echo '<p class="description">' . __('Check the features and amenities available at this mall.', 'mallfinder') . '</p>';
    
    $amenities = array(
        'parking' => __('Parking Available', 'mallfinder'),
        'free_parking' => __('Free Parking', 'mallfinder'),
        'valet_parking' => __('Valet Parking', 'mallfinder'),
        'wifi' => __('Free WiFi', 'mallfinder'),
        'food_court' => __('Food Court', 'mallfinder'),
        'restaurant' => __('Restaurant(s)', 'mallfinder'),
        'cafe' => __('Cafe/Coffee Shop', 'mallfinder'),
        'movie_theater' => __('Movie Theater/Cinema', 'mallfinder'),
        'gaming_zone' => __('Gaming Zone/Arcade', 'mallfinder'),
        'kids_play_area' => __('Kids Play Area', 'mallfinder'),
        'restrooms' => __('Public Restrooms', 'mallfinder'),
        'wheelchair_access' => __('Wheelchair Accessible', 'mallfinder'),
        'atm' => __('ATM Available', 'mallfinder'),
        'prayer_room' => __('Prayer Room', 'mallfinder'),
        'nursing_room' => __('Nursing/Mother\'s Room', 'mallfinder'),
        'locker' => __('Locker/Storage', 'mallfinder'),
        'pet_friendly' => __('Pet Friendly', 'mallfinder'),
        'outdoor_seating' => __('Outdoor Seating', 'mallfinder'),
        'live_events' => __('Live Events Space', 'mallfinder'),
        'ev_charging' => __('EV Charging Station', 'mallfinder'),
    );
    
    $selected_amenities = get_post_meta($post->ID, '_mall_amenities', true);
    if (!is_array($selected_amenities)) {
        $selected_amenities = array();
    }
    
    echo '<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px;">';
    foreach ($amenities as $key => $label) {
        $checked = in_array($key, $selected_amenities) ? 'checked' : '';
        echo '<label style="display: flex; align-items: center; gap: 8px; font-weight: normal;">';
        echo '<input type="checkbox" name="mall_amenities[]" value="' . esc_attr($key) . '" ' . $checked . '>';
        echo esc_html($label);
        echo '</label>';
    }
    echo '</div>';
}

function mallfinder_save_mall_meta($post_id) {
    if (!isset($_POST['mallfinder_mall_meta_nonce']) || !wp_verify_nonce($_POST['mallfinder_mall_meta_nonce'], 'mallfinder_mall_meta')) {
        return;
    }
    
    $fields = array('address', 'area', 'city', 'state', 'pincode', 'latitude', 'longitude', 'phone', 'website', 'opening_hours', 'total_stores', 'category');
    
    foreach ($fields as $field) {
        if (isset($_POST['mall_' . $field])) {
            if ($field === 'website') {
                update_post_meta($post_id, '_mall_' . $field, esc_url_raw($_POST['mall_' . $field]));
            } else {
                update_post_meta($post_id, '_mall_' . $field, sanitize_text_field($_POST['mall_' . $field]));
            }
        }
    }
    
    // === SAVE AMENITIES ===
    if (isset($_POST['mall_amenities']) && is_array($_POST['mall_amenities'])) {
        update_post_meta($post_id, '_mall_amenities', array_map('sanitize_text_field', $_POST['mall_amenities']));
    } else {
        delete_post_meta($post_id, '_mall_amenities');
    }
}
add_action('save_post_mall', 'mallfinder_save_mall_meta');

/**
 * Add Meta Box for Store Details
 */
function mallfinder_add_store_meta_box() {
    add_meta_box(
        'store_details',
        __('Store Details', 'mallfinder'),
        'mallfinder_store_meta_box_callback',
        'store',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mallfinder_add_store_meta_box');

function mallfinder_store_meta_box_callback($post) {
    wp_nonce_field('mallfinder_store_meta', 'mallfinder_store_meta_nonce');
    
    $fields = array(
        'mall_id' => __('Mall', 'mallfinder'),
        'floor' => __('Floor', 'mallfinder'),
        'unit' => __('Unit Number', 'mallfinder'),
        'phone' => __('Phone', 'mallfinder'),
        'website' => __('Website', 'mallfinder'),
        'opening_hours' => __('Opening Hours', 'mallfinder'),
        'category' => __('Category', 'mallfinder'),
        'status' => __('Status', 'mallfinder'),
    );
    
    // Get malls for dropdown
    $malls = get_posts(array('post_type' => 'mall', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
    
    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, '_store_' . $key, true);
        echo '<tr>';
        echo '<th><label for="store_' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
        echo '<td>';
        
        if ($key === 'mall_id') {
            echo '<select name="store_' . esc_attr($key) . '" id="store_' . esc_attr($key) . '">';
            echo '<option value="">' . __('Select Mall', 'mallfinder') . '</option>';
            foreach ($malls as $mall) {
                echo '<option value="' . $mall->ID . '"' . selected($value, $mall->ID, false) . '>' . esc_html($mall->post_title) . '</option>';
            }
            echo '</select>';
        } elseif ($key === 'status') {
            echo '<select name="store_' . esc_attr($key) . '" id="store_' . esc_attr($key) . '">';
            echo '<option value="OPEN"' . selected($value, 'OPEN', false) . '>' . __('Open', 'mallfinder') . '</option>';
            echo '<option value="COMING_SOON"' . selected($value, 'COMING_SOON', false) . '>' . __('Coming Soon', 'mallfinder') . '</option>';
            echo '<option value="CLOSED"' . selected($value, 'CLOSED', false) . '>' . __('Closed', 'mallfinder') . '</option>';
            echo '</select>';
        } else {
            echo '<input type="text" name="store_' . esc_attr($key) . '" id="store_' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text">';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</table>';
}

function mallfinder_save_store_meta($post_id) {
    if (!isset($_POST['mallfinder_store_meta_nonce']) || !wp_verify_nonce($_POST['mallfinder_store_meta_nonce'], 'mallfinder_store_meta')) {
        return;
    }
    
    $fields = array('mall_id', 'floor', 'unit', 'phone', 'website', 'opening_hours', 'category', 'status');
    
    foreach ($fields as $field) {
        if (isset($_POST['store_' . $field])) {
            update_post_meta($post_id, '_store_' . $field, sanitize_text_field($_POST['store_' . $field]));
        }
    }
}
add_action('save_post_store', 'mallfinder_save_store_meta');

/**
 * Helper Functions
 */
function mallfinder_get_mall_image($post_id) {
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail_url($post_id, 'mall-thumbnail');
    }
    return get_template_directory_uri() . '/assets/images/default-mall.jpg';
}

function mallfinder_get_store_image($post_id) {
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail_url($post_id, 'store-logo');
    }
    return get_template_directory_uri() . '/assets/images/default-store.png';
}

function mallfinder_get_mall_stores_count($mall_id) {
    $stores = get_posts(array(
        'post_type' => 'store',
        'posts_per_page' => -1,
        'meta_key' => '_store_mall_id',
        'meta_value' => $mall_id,
        'fields' => 'ids',
        'post_status' => 'publish',
    ));
    return count($stores);
}

function mallfinder_format_address($mall_id) {
    $address = get_post_meta($mall_id, '_mall_address', true);
    $area = get_post_meta($mall_id, '_mall_area', true);
    $city = get_post_meta($mall_id, '_mall_city', true);
    $state = get_post_meta($mall_id, '_mall_state', true);
    $pincode = get_post_meta($mall_id, '_mall_pincode', true);
    
    $parts = array_filter(array($address, $area, $city, $state, $pincode));
    return implode(', ', $parts);
}

function mallfinder_get_setting($key, $default = '') {
    return get_option('mallfinder_' . $key, $default);
}

/**
 * Theme Settings Page
 */
function mallfinder_add_settings_page() {
    add_menu_page(
        __('MallFinder Settings', 'mallfinder'),
        __('MallFinder Settings', 'mallfinder'),
        'manage_options',
        'mallfinder-settings',
        'mallfinder_settings_page_html',
        'dashicons-admin-settings',
        60
    );
}
add_action('admin_menu', 'mallfinder_add_settings_page');

// Enqueue media uploader on settings page
function mallfinder_admin_scripts($hook) {
    if ($hook === 'toplevel_page_mallfinder-settings') {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'mallfinder_admin_scripts');

function mallfinder_register_settings() {
    register_setting('mallfinder_settings', 'mallfinder_site_name');
    register_setting('mallfinder_settings', 'mallfinder_site_tagline');
    register_setting('mallfinder_settings', 'mallfinder_facebook');
    register_setting('mallfinder_settings', 'mallfinder_twitter');
    register_setting('mallfinder_settings', 'mallfinder_instagram');
    register_setting('mallfinder_settings', 'mallfinder_linkedin');
    register_setting('mallfinder_settings', 'mallfinder_youtube');
    register_setting('mallfinder_settings', 'mallfinder_whatsapp');
    register_setting('mallfinder_settings', 'mallfinder_default_lat');
    register_setting('mallfinder_settings', 'mallfinder_default_lng');
    register_setting('mallfinder_settings', 'mallfinder_default_zoom');
    
    // Left Sidebar Ad Settings
    register_setting('mallfinder_settings', 'mallfinder_left_sidebar_enable');
    register_setting('mallfinder_settings', 'mallfinder_left_ad_type');
    register_setting('mallfinder_settings', 'mallfinder_left_ad_image');
    register_setting('mallfinder_settings', 'mallfinder_left_ad_url');
    register_setting('mallfinder_settings', 'mallfinder_left_ad_code');
    
    // Right Sidebar Ad Settings
    register_setting('mallfinder_settings', 'mallfinder_right_sidebar_enable');
    register_setting('mallfinder_settings', 'mallfinder_right_ad_type');
    register_setting('mallfinder_settings', 'mallfinder_right_ad_image');
    register_setting('mallfinder_settings', 'mallfinder_right_ad_url');
    register_setting('mallfinder_settings', 'mallfinder_right_ad_code');
    
    // Header Ad Settings
    register_setting('mallfinder_settings', 'mallfinder_header_ad_enable');
    register_setting('mallfinder_settings', 'mallfinder_header_ad_type');
    register_setting('mallfinder_settings', 'mallfinder_header_ad_image');
    register_setting('mallfinder_settings', 'mallfinder_header_ad_url');
    register_setting('mallfinder_settings', 'mallfinder_header_ad_code');
    
    // Footer Ad Settings
    register_setting('mallfinder_settings', 'mallfinder_footer_ad_enable');
    register_setting('mallfinder_settings', 'mallfinder_footer_ad_type');
    register_setting('mallfinder_settings', 'mallfinder_footer_ad_image');
    register_setting('mallfinder_settings', 'mallfinder_footer_ad_url');
    register_setting('mallfinder_settings', 'mallfinder_footer_ad_code');
}
add_action('admin_init', 'mallfinder_register_settings');

function mallfinder_settings_page_html() {
    // Handle image URL save via AJAX
    if (isset($_POST['mallfinder_save_image']) && isset($_POST['image_url']) && isset($_POST['image_field'])) {
        check_admin_referer('mallfinder_image_upload');
        $image_url = esc_url_raw($_POST['image_url']);
        $field = sanitize_text_field($_POST['image_field']);
        if (in_array($field, array('mallfinder_left_ad_image', 'mallfinder_right_ad_image'))) {
            update_option($field, $image_url);
            wp_send_json_success(array('url' => $image_url));
        }
        wp_send_json_error();
    }
    ?>
    <div class="wrap">
        <h1><?php _e('MallFinder Theme Settings', 'mallfinder'); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="#general-settings" class="nav-tab nav-tab-active"><?php _e('General', 'mallfinder'); ?></a>
            <a href="#header-ad-settings" class="nav-tab"><?php _e('Header Ad', 'mallfinder'); ?></a>
            <a href="#left-sidebar-settings" class="nav-tab"><?php _e('Left Sidebar Ads', 'mallfinder'); ?></a>
            <a href="#right-sidebar-settings" class="nav-tab"><?php _e('Right Sidebar Ads', 'mallfinder'); ?></a>
            <a href="#footer-ad-settings" class="nav-tab"><?php _e('Footer Ad', 'mallfinder'); ?></a>
        </h2>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('mallfinder_settings');
            wp_nonce_field('mallfinder_image_upload', 'mallfinder_image_nonce');
            ?>
            
            <!-- General Settings Tab -->
            <div id="general-settings" class="settings-tab active">
                <table class="form-table">
                    <tr>
                        <th><?php _e('Site Name', 'mallfinder'); ?></th>
                        <td><input type="text" name="mallfinder_site_name" value="<?php echo esc_attr(get_option('mallfinder_site_name', 'MallFinder')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Site Tagline', 'mallfinder'); ?></th>
                        <td><input type="text" name="mallfinder_site_tagline" value="<?php echo esc_attr(get_option('mallfinder_site_tagline', 'Discover Shopping Destinations')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Facebook URL', 'mallfinder'); ?></th>
                        <td><input type="url" name="mallfinder_facebook" value="<?php echo esc_attr(get_option('mallfinder_facebook')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Twitter URL', 'mallfinder'); ?></th>
                        <td><input type="url" name="mallfinder_twitter" value="<?php echo esc_attr(get_option('mallfinder_twitter')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Instagram URL', 'mallfinder'); ?></th>
                        <td><input type="url" name="mallfinder_instagram" value="<?php echo esc_attr(get_option('mallfinder_instagram')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('LinkedIn URL', 'mallfinder'); ?></th>
                        <td><input type="url" name="mallfinder_linkedin" value="<?php echo esc_attr(get_option('mallfinder_linkedin')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('YouTube URL', 'mallfinder'); ?></th>
                        <td><input type="url" name="mallfinder_youtube" value="<?php echo esc_attr(get_option('mallfinder_youtube')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('WhatsApp Number', 'mallfinder'); ?></th>
                        <td><input type="text" name="mallfinder_whatsapp" value="<?php echo esc_attr(get_option('mallfinder_whatsapp')); ?>" class="regular-text" placeholder="e.g., 919876543210"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Default Map Latitude', 'mallfinder'); ?></th>
                        <td><input type="text" name="mallfinder_default_lat" value="<?php echo esc_attr(get_option('mallfinder_default_lat', '40.7128')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Default Map Longitude', 'mallfinder'); ?></th>
                        <td><input type="text" name="mallfinder_default_lng" value="<?php echo esc_attr(get_option('mallfinder_default_lng', '-74.0060')); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><?php _e('Default Map Zoom', 'mallfinder'); ?></th>
                        <td><input type="number" name="mallfinder_default_zoom" value="<?php echo esc_attr(get_option('mallfinder_default_zoom', '12')); ?>" class="small-text"></td>
                    </tr>
                </table>
            </div>
            
            <!-- Left Sidebar Settings Tab -->
            <div id="left-sidebar-settings" class="settings-tab" style="display:none;">
                <h2><?php _e('Left Sidebar Advertisement', 'mallfinder'); ?></h2>
                <p class="description"><?php _e('Configure the left sidebar advertisement on the homepage.', 'mallfinder'); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Left Sidebar', 'mallfinder'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mallfinder_left_sidebar_enable" value="1" <?php checked(get_option('mallfinder_left_sidebar_enable'), '1'); ?>>
                                <?php _e('Show left sidebar with ads', 'mallfinder'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Ad Type', 'mallfinder'); ?></th>
                        <td>
                            <select name="mallfinder_left_ad_type" id="left_ad_type">
                                <option value="image" <?php selected(get_option('mallfinder_left_ad_type'), 'image'); ?>><?php _e('Image Ad', 'mallfinder'); ?></option>
                                <option value="google" <?php selected(get_option('mallfinder_left_ad_type'), 'google'); ?>><?php _e('Google AdSense Code', 'mallfinder'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="left-image-fields">
                        <th><?php _e('Ad Image', 'mallfinder'); ?></th>
                        <td>
                            <?php $left_image = get_option('mallfinder_left_ad_image'); ?>
                            <div id="left-image-preview" style="margin-bottom: 10px;">
                                <?php if ($left_image) : ?>
                                    <img src="<?php echo esc_url($left_image); ?>" style="max-width: 200px; height: auto; border: 1px solid #ccc;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="mallfinder_left_ad_image" id="left_ad_image" value="<?php echo esc_attr($left_image); ?>">
                            <button type="button" class="button upload-image-button" data-target="left_ad_image" data-preview="left-image-preview"><?php _e('Select/Upload Image', 'mallfinder'); ?></button>
                            <button type="button" class="button remove-image-button" data-target="left_ad_image" data-preview="left-image-preview" style="<?php echo $left_image ? '' : 'display:none;'; ?>"><?php _e('Remove Image', 'mallfinder'); ?></button>
                        </td>
                    </tr>
                    <tr class="left-image-fields">
                        <th><?php _e('Ad Link URL', 'mallfinder'); ?></th>
                        <td>
                            <input type="url" name="mallfinder_left_ad_url" value="<?php echo esc_attr(get_option('mallfinder_left_ad_url')); ?>" class="regular-text" placeholder="https://example.com">
                            <p class="description"><?php _e('The URL where users will be redirected when clicking the ad (opens in new window).', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                    <tr class="left-google-fields" style="display:none;">
                        <th><?php _e('Google AdSense Code', 'mallfinder'); ?></th>
                        <td>
                            <textarea name="mallfinder_left_ad_code" rows="6" class="large-text code"><?php echo esc_textarea(get_option('mallfinder_left_ad_code')); ?></textarea>
                            <p class="description"><?php _e('Paste your Google AdSense or other ad network code here.', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Right Sidebar Settings Tab -->
            <div id="right-sidebar-settings" class="settings-tab" style="display:none;">
                <h2><?php _e('Right Sidebar Advertisement', 'mallfinder'); ?></h2>
                <p class="description"><?php _e('Configure the right sidebar advertisement on the homepage.', 'mallfinder'); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Right Sidebar', 'mallfinder'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mallfinder_right_sidebar_enable" value="1" <?php checked(get_option('mallfinder_right_sidebar_enable'), '1'); ?>>
                                <?php _e('Show right sidebar with ads', 'mallfinder'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Ad Type', 'mallfinder'); ?></th>
                        <td>
                            <select name="mallfinder_right_ad_type" id="right_ad_type">
                                <option value="image" <?php selected(get_option('mallfinder_right_ad_type'), 'image'); ?>><?php _e('Image Ad', 'mallfinder'); ?></option>
                                <option value="google" <?php selected(get_option('mallfinder_right_ad_type'), 'google'); ?>><?php _e('Google AdSense Code', 'mallfinder'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="right-image-fields">
                        <th><?php _e('Ad Image', 'mallfinder'); ?></th>
                        <td>
                            <?php $right_image = get_option('mallfinder_right_ad_image'); ?>
                            <div id="right-image-preview" style="margin-bottom: 10px;">
                                <?php if ($right_image) : ?>
                                    <img src="<?php echo esc_url($right_image); ?>" style="max-width: 200px; height: auto; border: 1px solid #ccc;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="mallfinder_right_ad_image" id="right_ad_image" value="<?php echo esc_attr($right_image); ?>">
                            <button type="button" class="button upload-image-button" data-target="right_ad_image" data-preview="right-image-preview"><?php _e('Select/Upload Image', 'mallfinder'); ?></button>
                            <button type="button" class="button remove-image-button" data-target="right_ad_image" data-preview="right-image-preview" style="<?php echo $right_image ? '' : 'display:none;'; ?>"><?php _e('Remove Image', 'mallfinder'); ?></button>
                        </td>
                    </tr>
                    <tr class="right-image-fields">
                        <th><?php _e('Ad Link URL', 'mallfinder'); ?></th>
                        <td>
                            <input type="url" name="mallfinder_right_ad_url" value="<?php echo esc_attr(get_option('mallfinder_right_ad_url')); ?>" class="regular-text" placeholder="https://example.com">
                            <p class="description"><?php _e('The URL where users will be redirected when clicking the ad (opens in new window).', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                    <tr class="right-google-fields" style="display:none;">
                        <th><?php _e('Google AdSense Code', 'mallfinder'); ?></th>
                        <td>
                            <textarea name="mallfinder_right_ad_code" rows="6" class="large-text code"><?php echo esc_textarea(get_option('mallfinder_right_ad_code')); ?></textarea>
                            <p class="description"><?php _e('Paste your Google AdSense or other ad network code here.', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Header Ad Settings Tab -->
            <div id="header-ad-settings" class="settings-tab" style="display:none;">
                <h2><?php _e('Header Advertisement', 'mallfinder'); ?></h2>
                <p class="description"><?php _e('Configure the header advertisement banner displayed below the navigation on all pages.', 'mallfinder'); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Header Ad', 'mallfinder'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mallfinder_header_ad_enable" value="1" <?php checked(get_option('mallfinder_header_ad_enable'), '1'); ?>>
                                <?php _e('Show header ad banner', 'mallfinder'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Ad Type', 'mallfinder'); ?></th>
                        <td>
                            <select name="mallfinder_header_ad_type" id="header_ad_type">
                                <option value="image" <?php selected(get_option('mallfinder_header_ad_type'), 'image'); ?>><?php _e('Image Ad', 'mallfinder'); ?></option>
                                <option value="google" <?php selected(get_option('mallfinder_header_ad_type'), 'google'); ?>><?php _e('Google AdSense Code', 'mallfinder'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="header-image-fields">
                        <th><?php _e('Ad Image', 'mallfinder'); ?></th>
                        <td>
                            <?php $header_image = get_option('mallfinder_header_ad_image'); ?>
                            <div id="header-image-preview" style="margin-bottom: 10px;">
                                <?php if ($header_image) : ?>
                                    <img src="<?php echo esc_url($header_image); ?>" style="max-width: 400px; height: auto; border: 1px solid #ccc;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="mallfinder_header_ad_image" id="header_ad_image" value="<?php echo esc_attr($header_image); ?>">
                            <button type="button" class="button upload-image-button" data-target="header_ad_image" data-preview="header-image-preview"><?php _e('Select/Upload Image', 'mallfinder'); ?></button>
                            <button type="button" class="button remove-image-button" data-target="header_ad_image" data-preview="header-image-preview" style="<?php echo $header_image ? '' : 'display:none;'; ?>"><?php _e('Remove Image', 'mallfinder'); ?></button>
                        </td>
                    </tr>
                    <tr class="header-image-fields">
                        <th><?php _e('Ad Link URL', 'mallfinder'); ?></th>
                        <td>
                            <input type="url" name="mallfinder_header_ad_url" value="<?php echo esc_attr(get_option('mallfinder_header_ad_url')); ?>" class="regular-text" placeholder="https://example.com">
                            <p class="description"><?php _e('The URL where users will be redirected when clicking the ad (opens in new window).', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                    <tr class="header-google-fields" style="display:none;">
                        <th><?php _e('Google AdSense Code', 'mallfinder'); ?></th>
                        <td>
                            <textarea name="mallfinder_header_ad_code" rows="6" class="large-text code"><?php echo esc_textarea(get_option('mallfinder_header_ad_code')); ?></textarea>
                            <p class="description"><?php _e('Paste your Google AdSense or other ad network code here.', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Footer Ad Settings Tab -->
            <div id="footer-ad-settings" class="settings-tab" style="display:none;">
                <h2><?php _e('Footer Advertisement', 'mallfinder'); ?></h2>
                <p class="description"><?php _e('Configure the footer advertisement banner displayed above the footer on all pages.', 'mallfinder'); ?></p>
                <table class="form-table">
                    <tr>
                        <th><?php _e('Enable Footer Ad', 'mallfinder'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="mallfinder_footer_ad_enable" value="1" <?php checked(get_option('mallfinder_footer_ad_enable'), '1'); ?>>
                                <?php _e('Show footer ad banner', 'mallfinder'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Ad Type', 'mallfinder'); ?></th>
                        <td>
                            <select name="mallfinder_footer_ad_type" id="footer_ad_type">
                                <option value="image" <?php selected(get_option('mallfinder_footer_ad_type'), 'image'); ?>><?php _e('Image Ad', 'mallfinder'); ?></option>
                                <option value="google" <?php selected(get_option('mallfinder_footer_ad_type'), 'google'); ?>><?php _e('Google AdSense Code', 'mallfinder'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="footer-image-fields">
                        <th><?php _e('Ad Image', 'mallfinder'); ?></th>
                        <td>
                            <?php $footer_image = get_option('mallfinder_footer_ad_image'); ?>
                            <div id="footer-image-preview" style="margin-bottom: 10px;">
                                <?php if ($footer_image) : ?>
                                    <img src="<?php echo esc_url($footer_image); ?>" style="max-width: 400px; height: auto; border: 1px solid #ccc;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="mallfinder_footer_ad_image" id="footer_ad_image" value="<?php echo esc_attr($footer_image); ?>">
                            <button type="button" class="button upload-image-button" data-target="footer_ad_image" data-preview="footer-image-preview"><?php _e('Select/Upload Image', 'mallfinder'); ?></button>
                            <button type="button" class="button remove-image-button" data-target="footer_ad_image" data-preview="footer-image-preview" style="<?php echo $footer_image ? '' : 'display:none;'; ?>"><?php _e('Remove Image', 'mallfinder'); ?></button>
                        </td>
                    </tr>
                    <tr class="footer-image-fields">
                        <th><?php _e('Ad Link URL', 'mallfinder'); ?></th>
                        <td>
                            <input type="url" name="mallfinder_footer_ad_url" value="<?php echo esc_attr(get_option('mallfinder_footer_ad_url')); ?>" class="regular-text" placeholder="https://example.com">
                            <p class="description"><?php _e('The URL where users will be redirected when clicking the ad (opens in new window).', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                    <tr class="footer-google-fields" style="display:none;">
                        <th><?php _e('Google AdSense Code', 'mallfinder'); ?></th>
                        <td>
                            <textarea name="mallfinder_footer_ad_code" rows="6" class="large-text code"><?php echo esc_textarea(get_option('mallfinder_footer_ad_code')); ?></textarea>
                            <p class="description"><?php _e('Paste your Google AdSense or other ad network code here.', 'mallfinder'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.settings-tab').hide();
            $(target).show();
        });
        
        // Toggle fields based on ad type
        function toggleAdFields(side) {
            var type = $('#' + side + '_ad_type').val();
            if (type === 'image') {
                $('.' + side + '-image-fields').show();
                $('.' + side + '-google-fields').hide();
            } else {
                $('.' + side + '-image-fields').hide();
                $('.' + side + '-google-fields').show();
            }
        }
        
        $('#left_ad_type').on('change', function() { toggleAdFields('left'); });
        $('#right_ad_type').on('change', function() { toggleAdFields('right'); });
        $('#header_ad_type').on('change', function() { toggleAdFields('header'); });
        $('#footer_ad_type').on('change', function() { toggleAdFields('footer'); });
        
        // Initial toggle
        toggleAdFields('left');
        toggleAdFields('right');
        toggleAdFields('header');
        toggleAdFields('footer');
        
        // WordPress Media Library Uploader
        var mediaUploader;
        
        $('.upload-image-button').on('click', function(e) {
            e.preventDefault();
            var targetInput = $(this).data('target');
            var previewDiv = $(this).data('preview');
            
            // If the uploader already exists, open it
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            // Create the media uploader
            mediaUploader = wp.media({
                title: '<?php _e('Select Ad Image', 'mallfinder'); ?>',
                button: {
                    text: '<?php _e('Use This Image', 'mallfinder'); ?>'
                },
                multiple: false
            });
            
            // When an image is selected, run a callback
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                var imageUrl = attachment.url;
                
                // Update the hidden input
                $('#' + targetInput).val(imageUrl);
                
                // Update the preview
                $('#' + previewDiv).html('<img src="' + imageUrl + '" style="max-width: 200px; height: auto; border: 1px solid #ccc;">');
                
                // Show remove button
                $('.remove-image-button[data-target="' + targetInput + '"]').show();
            });
            
            // Open the uploader dialog
            mediaUploader.open();
        });
        
        // Remove image button
        $('.remove-image-button').on('click', function(e) {
            e.preventDefault();
            var targetInput = $(this).data('target');
            var previewDiv = $(this).data('preview');
            
            // Clear the hidden input
            $('#' + targetInput).val('');
            
            // Clear the preview
            $('#' + previewDiv).html('');
            
            // Hide remove button
            $(this).hide();
        });
    });
    </script>
    <?php
}

/**
 * AJAX Search Suggestions
 */
function mallfinder_search_suggestions() {
    // Verify nonce but don't fail if missing for better UX
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'mallfinder_nonce')) {
        // Continue anyway for search functionality
    }
    
    $search = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
    $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
    
    if (strlen($search) < 2) {
        wp_send_json(array());
    }
    
    // Build base args for meta search
    $args = array(
        'post_type'      => 'mall',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
    );
    
    // Build meta query for search
    $search_meta_query = array(
        'relation' => 'OR',
        array(
            'key'     => '_mall_address',
            'value'   => $search,
            'compare' => 'LIKE',
        ),
        array(
            'key'     => '_mall_area',
            'value'   => $search,
            'compare' => 'LIKE',
        ),
        array(
            'key'     => '_mall_city',
            'value'   => $search,
            'compare' => 'LIKE',
        ),
        array(
            'key'     => '_mall_state',
            'value'   => $search,
            'compare' => 'LIKE',
        ),
    );
    
    // Build filter meta query
    $filter_meta_query = array('relation' => 'AND');
    
    if ($category !== 'all') {
        $filter_meta_query[] = array(
            'key'     => '_mall_category',
            'value'   => $category,
            'compare' => '=',
        );
    }
    
    // Combine search and filters
    if (count($filter_meta_query) > 1) {
        $args['meta_query'] = array(
            'relation' => 'AND',
            $filter_meta_query,
            $search_meta_query,
        );
    } else {
        $args['meta_query'] = $search_meta_query;
    }
    
    // First try meta search
    $malls = get_posts($args);
    
    // Also search by title
    $title_args = array(
        'post_type'      => 'mall',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        's'              => $search,
    );
    
    if (count($filter_meta_query) > 1) {
        $title_args['meta_query'] = $filter_meta_query;
    }
    
    $title_malls = get_posts($title_args);
    
    // Merge results and remove duplicates
    $found_ids = array();
    $all_malls = array();
    
    foreach ($malls as $mall) {
        if (!in_array($mall->ID, $found_ids)) {
            $found_ids[] = $mall->ID;
            $all_malls[] = $mall;
        }
    }
    
    foreach ($title_malls as $mall) {
        if (!in_array($mall->ID, $found_ids)) {
            $found_ids[] = $mall->ID;
            $all_malls[] = $mall;
        }
    }
    
    $results = array();
    
    foreach ($all_malls as $mall) {
        $mall_category = get_post_meta($mall->ID, '_mall_category', true);
        $address = mallfinder_format_address($mall->ID);
        $image = get_the_post_thumbnail_url($mall->ID, 'mall-thumbnail');
        
        $results[] = array(
            'id'        => $mall->ID,
            'title'     => $mall->post_title,
            'url'       => get_permalink($mall->ID),
            'address'   => $address,
            'category'  => $mall_category ?: 'existing',
            'image'     => $image ?: get_template_directory_uri() . '/assets/images/default-mall.jpg',
        );
    }
    
    wp_send_json($results);
}
add_action('wp_ajax_mallfinder_search', 'mallfinder_search_suggestions');
add_action('wp_ajax_nopriv_mallfinder_search', 'mallfinder_search_suggestions');

/**
 * Custom Search Query Filter
 */
function mallfinder_search_filter($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        $query->set('post_type', 'mall');
        
        $meta_query = $query->get('meta_query') ?: array();
        
        // Category filter
        $category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
        if ($category !== 'all') {
            $meta_query[] = array(
                'key'     => '_mall_category',
                'value'   => $category,
                'compare' => '=',
            );
        }
        
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }
    return $query;
}
add_action('pre_get_posts', 'mallfinder_search_filter');

/**
 * Flush Rewrite Rules on Activation
 */
function mallfinder_activate() {
    mallfinder_register_mall_post_type();
    mallfinder_register_store_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'mallfinder_activate');

/**
 * Social Share Buttons Function
 * 
 * @param string $title The title to share
 * @param string $url The URL to share (optional, defaults to current page)
 * @param string $style The style of buttons: 'full', 'compact', or 'hero'
 * @return string HTML output of social share buttons
 */
function mallfinder_social_share_buttons($title = '', $url = '', $style = 'full') {
    if (empty($url)) {
        $url = get_permalink();
    }
    
    if (empty($title)) {
        $title = get_the_title();
    }
    
    $encoded_url = rawurlencode($url);
    $encoded_title = rawurlencode($title);
    
    // Build share URLs
    $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
    $twitter_url = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
    $whatsapp_url = 'https://wa.me/?text=' . $encoded_title . '%20' . $encoded_url;
    $linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
    $telegram_url = 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title;
    $email_url = 'mailto:?subject=' . $encoded_title . '&body=' . __('Check this out: ', 'mallfinder') . $encoded_url;
    
    ob_start();
    
    if ($style === 'hero') {
        // Compact hero style for homepage hero section
        ?>
        <div class="hero-share-bar">
            <span class="share-label"><?php _e('Share:', 'mallfinder'); ?></span>
            <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="hero-share-btn facebook" title="<?php esc_attr_e('Share on Facebook', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" class="hero-share-btn twitter" title="<?php esc_attr_e('Share on X (Twitter)', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="hero-share-btn whatsapp" title="<?php esc_attr_e('Share on WhatsApp', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
            <button type="button" class="hero-share-btn copy-link" onclick="mallfinderCopyLink(this, '<?php echo esc_js($url); ?>')" title="<?php esc_attr_e('Copy Link', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </button>
        </div>
        <?php
    } elseif ($style === 'compact') {
        // Compact style for inline use
        ?>
        <div class="social-share-compact">
            <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn facebook" title="<?php esc_attr_e('Share on Facebook', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </a>
            <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn twitter" title="<?php esc_attr_e('Share on X (Twitter)', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn whatsapp" title="<?php esc_attr_e('Share on WhatsApp', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
            <button type="button" class="social-share-btn copy-link" onclick="mallfinderCopyLink(this, '<?php echo esc_js($url); ?>')" title="<?php esc_attr_e('Copy Link', 'mallfinder'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            </button>
        </div>
        <?php
    } else {
        // Full style - default with section wrapper
        ?>
        <div class="social-share-section">
            <div class="social-share-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                <?php _e('Share this page', 'mallfinder'); ?>
            </div>
            <div class="social-share-buttons">
                <a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    <span><?php _e('Facebook', 'mallfinder'); ?></span>
                </a>
                <a href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    <span><?php _e('X (Twitter)', 'mallfinder'); ?></span>
                </a>
                <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn whatsapp">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span><?php _e('WhatsApp', 'mallfinder'); ?></span>
                </a>
                <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn linkedin">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    <span><?php _e('LinkedIn', 'mallfinder'); ?></span>
                </a>
                <a href="<?php echo esc_url($telegram_url); ?>" target="_blank" rel="noopener noreferrer" class="social-share-btn telegram">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    <span><?php _e('Telegram', 'mallfinder'); ?></span>
                </a>
                <a href="<?php echo esc_url($email_url); ?>" class="social-share-btn email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span><?php _e('Email', 'mallfinder'); ?></span>
                </a>
                <button type="button" class="social-share-btn copy-link" onclick="mallfinderCopyLink(this, '<?php echo esc_js($url); ?>')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    <span><?php _e('Copy Link', 'mallfinder'); ?></span>
                </button>
            </div>
        </div>
        <?php
    }
    
    return ob_get_clean();
}

/**
 * JavaScript for Copy Link functionality
 */
function mallfinder_share_script() {
    ?>
    <script>
    function mallfinderCopyLink(btn, url) {
        navigator.clipboard.writeText(url).then(function() {
            var originalText = btn.querySelector('span');
            if (originalText) {
                var originalContent = originalText.textContent;
                originalText.textContent = '<?php echo esc_js(__('Copied!', 'mallfinder')); ?>';
                btn.classList.add('copied');
                setTimeout(function() {
                    originalText.textContent = originalContent;
                    btn.classList.remove('copied');
                }, 2000);
            } else {
                btn.classList.add('copied');
                setTimeout(function() {
                    btn.classList.remove('copied');
                }, 2000);
            }
            
            // Show tooltip
            var tooltip = document.createElement('div');
            tooltip.className = 'share-tooltip show';
            tooltip.textContent = '<?php echo esc_js(__('Link copied to clipboard!', 'mallfinder')); ?>';
            document.body.appendChild(tooltip);
            setTimeout(function() {
                tooltip.classList.remove('show');
                setTimeout(function() {
                    tooltip.remove();
                }, 300);
            }, 2000);
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            alert('<?php echo esc_js(__('Failed to copy link. Please copy manually: ', 'mallfinder')); ?>' + url);
        });
    }
    </script>
    <?php
}
add_action('wp_footer', 'mallfinder_share_script');

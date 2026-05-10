<?php
/**
 * MallFinder Theme Functions
 *
 * @package MallFinder
 */

/**
 * Theme Setup
 */
function mallfinder_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    add_theme_support('custom-logo');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mallfinder'),
        'footer'  => __('Footer Menu', 'mallfinder'),
    ));
    
    // Add image sizes
    add_image_size('mall-thumbnail', 400, 250, true);
    add_image_size('store-logo', 200, 200, true);
}
add_action('after_setup_theme', 'mallfinder_setup');

/**
 * Enqueue Styles and Scripts
 */
function mallfinder_enqueue_assets() {
    wp_enqueue_style('mallfinder-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // Enqueue Leaflet CSS and JS for maps
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
    
    wp_enqueue_script('mallfinder-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.0', true);
    
    // Localize script
    wp_localize_script('mallfinder-main', 'mallfinder_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('mallfinder_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'mallfinder_enqueue_assets');

/**
 * Register Mall Post Type
 */
function mallfinder_register_mall_post_type() {
    $labels = array(
        'name'               => __('Malls', 'mallfinder'),
        'singular_name'      => __('Mall', 'mallfinder'),
        'menu_name'          => __('Malls', 'mallfinder'),
        'add_new'            => __('Add New', 'mallfinder'),
        'add_new_item'       => __('Add New Mall', 'mallfinder'),
        'edit_item'          => __('Edit Mall', 'mallfinder'),
        'new_item'           => __('New Mall', 'mallfinder'),
        'view_item'          => __('View Mall', 'mallfinder'),
        'search_items'       => __('Search Malls', 'mallfinder'),
        'not_found'          => __('No malls found', 'mallfinder'),
        'not_found_in_trash' => __('No malls found in Trash', 'mallfinder'),
    );
    
    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-building',
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
        'add_new'            => __('Add New', 'mallfinder'),
        'add_new_item'       => __('Add New Store', 'mallfinder'),
        'edit_item'          => __('Edit Store', 'mallfinder'),
        'new_item'           => __('New Store', 'mallfinder'),
        'view_item'          => __('View Store', 'mallfinder'),
        'search_items'       => __('Search Stores', 'mallfinder'),
        'not_found'          => __('No stores found', 'mallfinder'),
        'not_found_in_trash' => __('No stores found in Trash', 'mallfinder'),
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
    
    // Features & Amenities Section
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
    
    // Save amenities
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
}
add_action('admin_init', 'mallfinder_register_settings');

function mallfinder_settings_page_html() {
    ?>
    <div class="wrap">
        <h1><?php _e('MallFinder Theme Settings', 'mallfinder'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('mallfinder_settings');
            ?>
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
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * AJAX Search Suggestions
 */
function mallfinder_search_suggestions() {
    $search = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
    
    if (strlen($search) < 2) {
        wp_send_json(array());
    }
    
    $args = array(
        'post_type'      => 'mall',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        'meta_query'     => array(
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
        ),
    );
    
    $malls = get_posts($args);
    
    $title_args = array(
        'post_type'      => 'mall',
        'posts_per_page' => 10,
        'post_status'    => 'publish',
        's'              => $search,
    );
    
    $title_malls = get_posts($title_args);
    
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
        $category = get_post_meta($mall->ID, '_mall_category', true);
        $address = mallfinder_format_address($mall->ID);
        $image = get_the_post_thumbnail_url($mall->ID, 'mall-thumbnail');
        
        $results[] = array(
            'id'        => $mall->ID,
            'title'     => $mall->post_title,
            'url'       => get_permalink($mall->ID),
            'address'   => $address,
            'category'  => $category ?: 'existing',
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

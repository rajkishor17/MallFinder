<?php
/**
 * MallFinder Theme Functions
 *
 * @package MallFinder
 * @version 2.1.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme version constant
define('MALLFINDER_VERSION', '2.1.0');

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
    add_theme_support('customize-selective-refresh-widgets');

    // Image sizes
    add_image_size('mall-thumbnail', 400, 300, true);
    add_image_size('mall-hero', 1920, 600, true);
    add_image_size('store-logo', 200, 200, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mallfinder'),
        'footer'  => __('Footer Menu', 'mallfinder'),
    ));

    // Load text domain
    load_theme_textdomain('mallfinder', get_template_directory() . '/languages');
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

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'mallfinder_scripts');

/**
 * Include Required Files
 */
function mallfinder_include_files() {
    $inc_files = array(
        'custom-post-types.php',
        'taxonomies.php',
        'meta-boxes.php',
        'theme-settings.php',
        'ajax-handlers.php',
        'widget-areas.php',
        'template-functions.php',
        'default-menu.php',
    );

    foreach ($inc_files as $file) {
        $file_path = get_template_directory() . '/inc/' . $file;
        if (file_exists($file_path)) {
            require_once $file_path;
        }
    }
}
add_action('after_setup_theme', 'mallfinder_include_files', 20);

/**
 * Activation Hook - Flush Rewrite Rules
 */
function mallfinder_activate() {
    // Flush rewrite rules on theme activation
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'mallfinder_activate');

/**
 * Deactivation Hook
 */
function mallfinder_deactivate() {
    flush_rewrite_rules();
}
add_action('switch_theme', 'mallfinder_deactivate');

/**
 * Helper Functions
 */

// Get mall featured image
function mallfinder_get_mall_image($post_id, $size = 'mall-thumbnail') {
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail_url($post_id, $size);
    }
    return get_template_directory_uri() . '/assets/images/default-mall.jpg';
}

// Get store logo
function mallfinder_get_store_logo($post_id) {
    $logo = get_post_meta($post_id, '_store_logo', true);
    if ($logo) {
        return wp_get_attachment_url($logo);
    }
    return get_template_directory_uri() . '/assets/images/default-store.png';
}

// Get mall stores count
function mallfinder_get_stores_count($mall_id) {
    $stores = get_posts(array(
        'post_type'      => 'store',
        'posts_per_page' => -1,
        'meta_key'       => '_store_mall',
        'meta_value'     => $mall_id,
        'fields'         => 'ids',
        'post_status'    => 'publish',
    ));
    return count($stores);
}

// Get malls by location
function mallfinder_get_malls_by_location($city_id = null, $area_id = null, $state_id = null) {
    $args = array(
        'post_type'      => 'mall',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    $meta_query = array();

    if ($area_id) {
        $meta_query[] = array(
            'key'     => '_mall_area',
            'value'   => $area_id,
            'compare' => '=',
        );
    } elseif ($city_id) {
        $meta_query[] = array(
            'key'     => '_mall_city',
            'value'   => $city_id,
            'compare' => '=',
        );
    } elseif ($state_id) {
        $meta_query[] = array(
            'key'     => '_mall_state',
            'value'   => $state_id,
            'compare' => '=',
        );
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    return get_posts($args);
}

// Get site setting
function mallfinder_get_setting($key, $default = '') {
    return get_option('mallfinder_' . $key, $default);
}

// Get social links
function mallfinder_get_social_links() {
    return array(
        'facebook'  => mallfinder_get_setting('facebook', ''),
        'twitter'   => mallfinder_get_setting('twitter', ''),
        'instagram' => mallfinder_get_setting('instagram', ''),
        'linkedin'  => mallfinder_get_setting('linkedin', ''),
        'youtube'   => mallfinder_get_setting('youtube', ''),
        'whatsapp'  => mallfinder_get_setting('whatsapp', ''),
    );
}

// Format address
function mallfinder_format_address($mall_id) {
    $address = get_post_meta($mall_id, '_mall_address', true);
    $area_id = get_post_meta($mall_id, '_mall_area', true);
    $city_id = get_post_meta($mall_id, '_mall_city', true);
    $state_id = get_post_meta($mall_id, '_mall_state', true);
    $pincode = get_post_meta($mall_id, '_mall_pincode', true);

    $area = $area_id ? get_the_title($area_id) : '';
    $city = $city_id ? get_the_title($city_id) : '';
    $state = $state_id ? get_the_title($state_id) : '';

    $parts = array_filter(array($address, $area, $city, $state, $pincode));
    return implode(', ', $parts);
}

// Get store categories
function mallfinder_get_store_categories($store_id) {
    $terms = get_the_terms($store_id, 'store_category');
    if ($terms && !is_wp_error($terms)) {
        return wp_list_pluck($terms, 'name');
    }
    return array();
}

/**
 * Add Custom Query Vars
 */
function mallfinder_query_vars($vars) {
    $vars[] = 'mall_state';
    $vars[] = 'mall_city';
    $vars[] = 'mall_area';
    return $vars;
}
add_filter('query_vars', 'mallfinder_query_vars');

/**
 * Modify Main Query for Archives
 */
function mallfinder_pre_get_posts($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Malls archive
        if (is_post_type_archive('mall')) {
            $query->set('posts_per_page', 12);
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
        }

        // Stores archive
        if (is_post_type_archive('store')) {
            $query->set('posts_per_page', 20);
        }
    }
}
add_action('pre_get_posts', 'mallfinder_pre_get_posts');

/**
 * Body Classes
 */
function mallfinder_body_class($classes) {
    if (is_singular('mall')) {
        $classes[] = 'single-mall-page';
    } elseif (is_singular('store')) {
        $classes[] = 'single-store-page';
    } elseif (is_post_type_archive('mall')) {
        $classes[] = 'malls-archive-page';
    } elseif (is_post_type_archive('store')) {
        $classes[] = 'stores-archive-page';
    }

    return $classes;
}
add_filter('body_class', 'mallfinder_body_class');

/**
 * Disable Gutenberg for Custom Post Types
 */
function mallfinder_disable_gutenberg($use_block_editor, $post_type) {
    $disabled_types = array('mall', 'store', 'city', 'state', 'area');

    if (in_array($post_type, $disabled_types)) {
        return false;
    }

    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'mallfinder_disable_gutenberg', 10, 2);

<?php
/**
 * MallFinder Theme Functions
 * 
 * @package MallFinder
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Theme Constants
define('MALLFINDER_VERSION', '1.0.0');
define('MALLFINDER_DIR', get_template_directory());
define('MALLFINDER_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function mallfinder_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'mallfinder'),
        'footer'  => __('Footer Menu', 'mallfinder'),
    ));
    
    // Load text domain
    load_theme_textdomain('mallfinder', MALLFINDER_DIR . '/languages');
}
add_action('after_setup_theme', 'mallfinder_setup');

/**
 * Enqueue Scripts and Styles
 */
function mallfinder_scripts() {
    // Main stylesheet
    wp_enqueue_style('mallfinder-style', MALLFINDER_URI . '/style.css', array(), MALLFINDER_VERSION);
    
    // Leaflet CSS for maps
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
    
    // Leaflet JS
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
    
    // Theme JavaScript
    wp_enqueue_script('mallfinder-main', MALLFINDER_URI . '/assets/js/main.js', array('jquery', 'leaflet-js'), MALLFINDER_VERSION, true);
    
    // Localize script with data
    wp_localize_script('mallfinder-main', 'mallfinderData', array(
        'ajaxUrl'      => admin_url('admin-ajax.php'),
        'nonce'        => wp_create_nonce('mallfinder_nonce'),
        'defaultLat'   => get_option('mallfinder_default_lat', '20.5937'),
        'defaultLng'   => get_option('mallfinder_default_lng', '78.9629'),
        'defaultZoom'  => get_option('mallfinder_default_zoom', '5'),
        'homeUrl'      => home_url(),
        'currentLang'  => get_locale(),
    ));
}
add_action('wp_enqueue_scripts', 'mallfinder_scripts');

/**
 * Include Required Files
 */
require_once MALLFINDER_DIR . '/inc/custom-post-types.php';
require_once MALLFINDER_DIR . '/inc/taxonomies.php';
require_once MALLFINDER_DIR . '/inc/meta-boxes.php';
require_once MALLFINDER_DIR . '/inc/admin-settings.php';
require_once MALLFINDER_DIR . '/inc/social-media.php';
require_once MALLFINDER_DIR . '/inc/helper-functions.php';

/**
 * Register Widget Areas
 */
function mallfinder_widgets_init() {
    register_sidebar(array(
        'name'          => __('Sidebar', 'mallfinder'),
        'id'            => 'sidebar-1',
        'description'   => __('Add widgets here.', 'mallfinder'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer 1', 'mallfinder'),
        'id'            => 'footer-1',
        'description'   => __('Footer widget area 1.', 'mallfinder'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'mallfinder_widgets_init');

/**
 * Custom Walker for Navigation
 */
class MallFinder_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        
        $output .= '<a href="' . esc_url($item->url) . '" class="' . esc_attr($class_names) . '">';
        $output .= esc_html($item->title);
        $output .= '</a>';
    }
}

/**
 * Get Mall Count by Status
 */
function mallfinder_get_mall_count($status = '') {
    $args = array(
        'post_type'      => 'mall',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    );
    
    if ($status) {
        $args['meta_query'] = array(
            array(
                'key'     => '_mall_status',
                'value'   => $status,
                'compare' => '=',
            ),
        );
    }
    
    $query = new WP_Query($args);
    return $query->found_posts;
}

/**
 * Get Store Count for Mall
 */
function mallfinder_get_store_count($mall_id) {
    $args = array(
        'post_type'      => 'store',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key'     => '_store_mall_id',
                'value'   => $mall_id,
                'compare' => '=',
            ),
        ),
    );
    
    $query = new WP_Query($args);
    return $query->found_posts;
}

/**
 * AJAX: Get Malls for Map
 */
function mallfinder_ajax_get_malls() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $args = array(
        'post_type'      => 'mall',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );
    
    $state = isset($_GET['state']) ? sanitize_text_field($_GET['state']) : '';
    $city = isset($_GET['city']) ? sanitize_text_field($_GET['city']) : '';
    $area = isset($_GET['area']) ? sanitize_text_field($_GET['area']) : '';
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    
    if ($state) {
        $args['tax_query'][] = array(
            'taxonomy' => 'state',
            'field'    => 'slug',
            'terms'    => $state,
        );
    }
    
    if ($city) {
        $args['tax_query'][] = array(
            'taxonomy' => 'city',
            'field'    => 'slug',
            'terms'    => $city,
        );
    }
    
    if ($area) {
        $args['tax_query'][] = array(
            'taxonomy' => 'area',
            'field'    => 'slug',
            'terms'    => $area,
        );
    }
    
    if ($status) {
        $args['meta_query'][] = array(
            'key'     => '_mall_status',
            'value'   => $status,
            'compare' => '=',
        );
    }
    
    $query = new WP_Query($args);
    $malls = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $mall_id = get_the_ID();
            
            $lat = get_post_meta($mall_id, '_mall_latitude', true);
            $lng = get_post_meta($mall_id, '_mall_longitude', true);
            
            if ($lat && $lng) {
                $malls[] = array(
                    'id'        => $mall_id,
                    'title'     => get_the_title(),
                    'lat'       => floatval($lat),
                    'lng'       => floatval($lng),
                    'address'   => get_post_meta($mall_id, '_mall_address', true),
                    'status'    => get_post_meta($mall_id, '_mall_status', true),
                    'url'       => get_permalink(),
                    'image'     => get_the_post_thumbnail_url($mall_id, 'medium'),
                );
            }
        }
    }
    wp_reset_postdata();
    
    wp_send_json_success($malls);
}
add_action('wp_ajax_get_malls', 'mallfinder_ajax_get_malls');
add_action('wp_ajax_nopriv_get_malls', 'mallfinder_ajax_get_malls');

/**
 * AJAX: Search Malls
 */
function mallfinder_ajax_search_malls() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 12;
    
    $args = array(
        'post_type'      => 'mall',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        's'              => $search,
    );
    
    if ($status) {
        $args['meta_query'][] = array(
            'key'     => '_mall_status',
            'value'   => $status,
            'compare' => '=',
        );
    }
    
    $query = new WP_Query($args);
    $malls = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $mall_id = get_the_ID();
            
            $terms = wp_get_post_terms($mall_id, array('state', 'city', 'area'));
            $location = array();
            foreach ($terms as $term) {
                $location[$term->taxonomy] = $term->name;
            }
            
            $malls[] = array(
                'id'        => $mall_id,
                'title'     => get_the_title(),
                'address'   => get_post_meta($mall_id, '_mall_address', true),
                'status'    => get_post_meta($mall_id, '_mall_status', true),
                'url'       => get_permalink(),
                'image'     => get_the_post_thumbnail_url($mall_id, 'medium'),
                'location'  => $location,
            );
        }
    }
    wp_reset_postdata();
    
    wp_send_json_success(array(
        'malls'      => $malls,
        'total'      => $query->found_posts,
        'totalPages' => $query->max_num_pages,
        'currentPage' => $page,
    ));
}
add_action('wp_ajax_search_malls', 'mallfinder_ajax_search_malls');
add_action('wp_ajax_nopriv_search_malls', 'mallfinder_ajax_search_malls');

/**
 * Flush Rewrite Rules on Theme Activation
 */
function mallfinder_activate() {
    // Register post types and taxonomies
    mallfinder_register_post_types();
    mallfinder_register_taxonomies();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'mallfinder_activate');

/**
 * Body Classes
 */
function mallfinder_body_classes($classes) {
    if (is_singular('mall')) {
        $classes[] = 'single-mall-page';
        $status = get_post_meta(get_the_ID(), '_mall_status', true);
        if ($status) {
            $classes[] = 'mall-status-' . $status;
        }
    }
    
    if (is_post_type_archive('mall') || is_tax(array('state', 'city', 'area'))) {
        $classes[] = 'mall-archive-page';
    }
    
    return $classes;
}
add_filter('body_class', 'mallfinder_body_classes');

/**
 * Disable Gutenberg for Custom Post Types
 */
function mallfinder_disable_gutenberg($use_block_editor, $post_type) {
    if (in_array($post_type, array('mall', 'store', 'advertisement'))) {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'mallfinder_disable_gutenberg', 10, 2);

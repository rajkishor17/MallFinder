<?php
/**
 * AJAX Handlers
 *
 * @package MallFinder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Search Malls AJAX
 */
function mallfinder_search_malls() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $search = sanitize_text_field($_POST['search'] ?? '');
    $state = intval($_POST['state'] ?? 0);
    $city = intval($_POST['city'] ?? 0);
    $area = intval($_POST['area'] ?? 0);
    $page = intval($_POST['page'] ?? 1);
    $per_page = intval($_POST['per_page'] ?? 12);
    
    $args = array(
        'post_type'      => 'mall',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'post_status'    => 'publish',
    );
    
    if ($search) {
        $args['s'] = $search;
    }
    
    $meta_query = array();
    
    if ($area) {
        $meta_query[] = array(
            'key'     => '_mall_area',
            'value'   => $area,
            'compare' => '=',
        );
    } elseif ($city) {
        $meta_query[] = array(
            'key'     => '_mall_city',
            'value'   => $city,
            'compare' => '=',
        );
    } elseif ($state) {
        $meta_query[] = array(
            'key'     => '_mall_state',
            'value'   => $state,
            'compare' => '=',
        );
    }
    
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    
    $query = new WP_Query($args);
    
    $results = array();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $mall_id = get_the_ID();
            
            $results[] = array(
                'id'        => $mall_id,
                'title'     => get_the_title(),
                'permalink' => get_permalink(),
                'image'     => get_the_post_thumbnail_url($mall_id, 'mall-thumbnail'),
                'address'   => mallfinder_format_address($mall_id),
                'latitude'  => get_post_meta($mall_id, '_mall_latitude', true),
                'longitude' => get_post_meta($mall_id, '_mall_longitude', true),
                'stores'    => mallfinder_get_stores_count($mall_id),
            );
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success(array(
        'malls'       => $results,
        'total'       => $query->found_posts,
        'total_pages' => $query->max_num_pages,
        'current_page' => $page,
    ));
}
add_action('wp_ajax_mallfinder_search_malls', 'mallfinder_search_malls');
add_action('wp_ajax_nopriv_mallfinder_search_malls', 'mallfinder_search_malls');

/**
 * Get Mall Map Data AJAX
 */
function mallfinder_get_map_data() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $state = intval($_POST['state'] ?? 0);
    $city = intval($_POST['city'] ?? 0);
    $area = intval($_POST['area'] ?? 0);
    
    $args = array(
        'post_type'      => 'mall',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );
    
    $meta_query = array(
        array(
            'key'     => '_mall_latitude',
            'compare' => 'EXISTS',
        ),
        array(
            'key'     => '_mall_longitude',
            'compare' => 'EXISTS',
        ),
    );
    
    if ($area) {
        $meta_query[] = array(
            'key'     => '_mall_area',
            'value'   => $area,
            'compare' => '=',
        );
    } elseif ($city) {
        $meta_query[] = array(
            'key'     => '_mall_city',
            'value'   => $city,
            'compare' => '=',
        );
    } elseif ($state) {
        $meta_query[] = array(
            'key'     => '_mall_state',
            'value'   => $state,
            'compare' => '=',
        );
    }
    
    $args['meta_query'] = $meta_query;
    
    $malls = get_posts($args);
    
    $markers = array();
    
    foreach ($malls as $mall) {
        $lat = get_post_meta($mall->ID, '_mall_latitude', true);
        $lng = get_post_meta($mall->ID, '_mall_longitude', true);
        
        if ($lat && $lng) {
            $markers[] = array(
                'id'        => $mall->ID,
                'title'     => $mall->post_title,
                'permalink' => get_permalink($mall->ID),
                'lat'       => floatval($lat),
                'lng'       => floatval($lng),
                'address'   => mallfinder_format_address($mall->ID),
                'image'     => get_the_post_thumbnail_url($mall->ID, 'mall-thumbnail'),
            );
        }
    }
    
    wp_send_json_success(array(
        'markers' => $markers,
    ));
}
add_action('wp_ajax_mallfinder_get_map_data', 'mallfinder_get_map_data');
add_action('wp_ajax_nopriv_mallfinder_get_map_data', 'mallfinder_get_map_data');

/**
 * Get Stores by Mall AJAX
 */
function mallfinder_get_mall_stores() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $mall_id = intval($_POST['mall_id'] ?? 0);
    
    if (!$mall_id) {
        wp_send_json_error(array('message' => __('Mall ID is required', 'mallfinder')));
    }
    
    $stores = get_posts(array(
        'post_type'      => 'store',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_store_mall',
        'meta_value'     => $mall_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    
    $results = array();
    
    foreach ($stores as $store) {
        $categories = get_the_terms($store->ID, 'store_category');
        $category_names = $categories ? wp_list_pluck($categories, 'name') : array();
        
        $results[] = array(
            'id'         => $store->ID,
            'title'      => $store->post_title,
            'permalink'  => get_permalink($store->ID),
            'logo'       => mallfinder_get_store_logo($store->ID),
            'floor'      => get_post_meta($store->ID, '_store_floor', true),
            'unit'       => get_post_meta($store->ID, '_store_unit', true),
            'phone'      => get_post_meta($store->ID, '_store_phone', true),
            'categories' => $category_names,
        );
    }
    
    wp_send_json_success(array(
        'stores' => $results,
    ));
}
add_action('wp_ajax_mallfinder_get_mall_stores', 'mallfinder_get_mall_stores');
add_action('wp_ajax_nopriv_mallfinder_get_mall_stores', 'mallfinder_get_mall_stores');

/**
 * Get Cities by State AJAX
 */
function mallfinder_get_cities_by_state() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $state_id = intval($_POST['state_id'] ?? 0);
    
    if (!$state_id) {
        wp_send_json_error(array('message' => __('State ID is required', 'mallfinder')));
    }
    
    $cities = get_posts(array(
        'post_type'      => 'city',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_city_state',
        'meta_value'     => $state_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    
    $results = array();
    
    foreach ($cities as $city) {
        $results[] = array(
            'id'    => $city->ID,
            'title' => $city->post_title,
        );
    }
    
    wp_send_json_success(array(
        'cities' => $results,
    ));
}
add_action('wp_ajax_mallfinder_get_cities_by_state', 'mallfinder_get_cities_by_state');
add_action('wp_ajax_nopriv_mallfinder_get_cities_by_state', 'mallfinder_get_cities_by_state');

/**
 * Get Areas by City AJAX
 */
function mallfinder_get_areas_by_city() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $city_id = intval($_POST['city_id'] ?? 0);
    
    if (!$city_id) {
        wp_send_json_error(array('message' => __('City ID is required', 'mallfinder')));
    }
    
    $areas = get_posts(array(
        'post_type'      => 'area',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => '_area_city',
        'meta_value'     => $city_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    
    $results = array();
    
    foreach ($areas as $area) {
        $results[] = array(
            'id'    => $area->ID,
            'title' => $area->post_title,
        );
    }
    
    wp_send_json_success(array(
        'areas' => $results,
    ));
}
add_action('wp_ajax_mallfinder_get_areas_by_city', 'mallfinder_get_areas_by_city');
add_action('wp_ajax_nopriv_mallfinder_get_areas_by_city', 'mallfinder_get_areas_by_city');

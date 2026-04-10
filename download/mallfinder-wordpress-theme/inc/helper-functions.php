<?php
/**
 * Helper Functions
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Site Name
 */
function mallfinder_get_site_name() {
    return get_option('mallfinder_site_name', get_bloginfo('name'));
}

/**
 * Get Site Tagline
 */
function mallfinder_get_site_tagline() {
    return get_option('mallfinder_site_tagline', get_bloginfo('description'));
}

/**
 * Get Site Logo
 */
function mallfinder_get_logo() {
    $logo = get_option('mallfinder_logo');
    if ($logo) {
        return $logo;
    }
    
    // Fall back to custom logo
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
        return $logo_url;
    }
    
    return '';
}

/**
 * Get Mall Status
 */
function mallfinder_get_mall_status($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_mall_status', true) ?: 'existing';
}

/**
 * Get Mall Address
 */
function mallfinder_get_mall_address($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_mall_address', true);
}

/**
 * Get Mall Coordinates
 */
function mallfinder_get_mall_coordinates($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return array(
        'lat' => get_post_meta($post_id, '_mall_latitude', true),
        'lng' => get_post_meta($post_id, '_mall_longitude', true),
    );
}

/**
 * Get Mall Opening Hours
 */
function mallfinder_get_mall_opening_hours($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_mall_opening_hours', true);
}

/**
 * Get Mall Phone
 */
function mallfinder_get_mall_phone($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_mall_phone', true);
}

/**
 * Get Mall Website
 */
function mallfinder_get_mall_website($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_mall_website', true);
}

/**
 * Get Mall Expected Opening Date
 */
function mallfinder_get_mall_expected_opening($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_mall_expected_opening', true);
}

/**
 * Get Mall Location Terms
 */
function mallfinder_get_mall_location($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $location = array(
        'state' => '',
        'city'  => '',
        'area'  => '',
    );
    
    $state = wp_get_post_terms($post_id, 'state');
    $city = wp_get_post_terms($post_id, 'city');
    $area = wp_get_post_terms($post_id, 'area');
    
    if (!empty($state) && !is_wp_error($state)) {
        $location['state'] = $state[0]->name;
    }
    
    if (!empty($city) && !is_wp_error($city)) {
        $location['city'] = $city[0]->name;
    }
    
    if (!empty($area) && !is_wp_error($area)) {
        $location['area'] = $area[0]->name;
    }
    
    return $location;
}

/**
 * Get Mall Image
 */
function mallfinder_get_mall_image($post_id = null, $size = 'large') {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $image_url = get_the_post_thumbnail_url($post_id, $size);
    
    if (!$image_url) {
        $image_url = MALLFINDER_URI . '/assets/images/default-mall.jpg';
    }
    
    return $image_url;
}

/**
 * Get Store Mall
 */
function mallfinder_get_store_mall($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $mall_id = get_post_meta($post_id, '_store_mall_id', true);
    
    if ($mall_id) {
        return get_post($mall_id);
    }
    
    return null;
}

/**
 * Get Store Status
 */
function mallfinder_get_store_status($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_store_status', true) ?: 'open';
}

/**
 * Get Store Floor
 */
function mallfinder_get_store_floor($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_store_floor', true);
}

/**
 * Get Store Unit Number
 */
function mallfinder_get_store_unit($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_store_unit', true);
}

/**
 * Get Store Phone
 */
function mallfinder_get_store_phone($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_store_phone', true);
}

/**
 * Get Store Website
 */
function mallfinder_get_store_website($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return get_post_meta($post_id, '_store_website', true);
}

/**
 * Get Store Category
 */
function mallfinder_get_store_category($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $categories = wp_get_post_terms($post_id, 'store_category');
    
    if (!empty($categories) && !is_wp_error($categories)) {
        return $categories[0];
    }
    
    return null;
}

/**
 * Get Advertisement by Position
 */
function mallfinder_get_advertisement($position, $mall_id = null) {
    $args = array(
        'post_type'      => 'advertisement',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'key'     => '_ad_position',
                'value'   => $position,
                'compare' => '=',
            ),
            array(
                'key'     => '_ad_is_active',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_ad_sort_order',
        'order'          => 'ASC',
    );
    
    if ($mall_id) {
        $args['meta_query'][] = array(
            'relation' => 'OR',
            array(
                'key'     => '_ad_mall_id',
                'value'   => $mall_id,
                'compare' => '=',
            ),
            array(
                'key'     => '_ad_mall_id',
                'value'   => '',
                'compare' => '=',
            ),
        );
    }
    
    $ads = get_posts($args);
    
    if (!empty($ads)) {
        return $ads[0];
    }
    
    return null;
}

/**
 * Display Advertisement
 */
function mallfinder_display_advertisement($position, $mall_id = null) {
    $ad = mallfinder_get_advertisement($position, $mall_id);
    
    if (!$ad) {
        return;
    }
    
    $ad_id = $ad->ID;
    $type = get_post_meta($ad_id, '_ad_type', true);
    $image_url = get_post_meta($ad_id, '_ad_image_url', true);
    $link_url = get_post_meta($ad_id, '_ad_link_url', true);
    $html_code = get_post_meta($ad_id, '_ad_html_code', true);
    
    if ($type === 'html' && $html_code) {
        echo '<div class="advertisement advertisement-' . esc_attr($position) . '">';
        echo do_shortcode($html_code);
        echo '</div>';
    } elseif ($type === 'image' && $image_url) {
        echo '<div class="advertisement advertisement-' . esc_attr($position) . '">';
        if ($link_url) {
            echo '<a href="' . esc_url($link_url) . '" target="_blank" rel="noopener noreferrer">';
        }
        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($ad->post_title) . '" />';
        if ($link_url) {
            echo '</a>';
        }
        echo '</div>';
    }
}

/**
 * Get All States
 */
function mallfinder_get_states() {
    return get_terms(array(
        'taxonomy'   => 'state',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ));
}

/**
 * Get Cities by State
 */
function mallfinder_get_cities($state_id = null) {
    $args = array(
        'taxonomy'   => 'city',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    );
    
    if ($state_id) {
        $args['parent'] = $state_id;
    }
    
    return get_terms($args);
}

/**
 * Get Areas by City
 */
function mallfinder_get_areas($city_id = null) {
    $args = array(
        'taxonomy'   => 'area',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    );
    
    if ($city_id) {
        $args['parent'] = $city_id;
    }
    
    return get_terms($args);
}

/**
 * Get Store Categories
 */
function mallfinder_get_store_categories() {
    return get_terms(array(
        'taxonomy'   => 'store_category',
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ));
}

/**
 * Get Mall Amenities
 */
function mallfinder_get_mall_amenities($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return wp_get_post_terms($post_id, 'amenity');
}

/**
 * Get Mall Features
 */
function mallfinder_get_mall_features($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    return wp_get_post_terms($post_id, 'feature');
}

/**
 * Format Date for Display
 */
function mallfinder_format_date($date_string, $format = 'F j, Y') {
    if (empty($date_string)) {
        return '';
    }
    
    $date = strtotime($date_string);
    return date_i18n($format, $date);
}

/**
 * Truncate Text
 */
function mallfinder_truncate($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

/**
 * Display Go to Top Button
 */
function mallfinder_go_to_top() {
    ?>
    <button class="go-to-top" id="go-to-top">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
        <?php _e('Go to Top', 'mallfinder'); ?>
    </button>
    <?php
}

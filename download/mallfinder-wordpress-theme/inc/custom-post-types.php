<?php
/**
 * Custom Post Types
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Post Types
 */
function mallfinder_register_post_types() {
    
    // Mall Post Type
    $mall_labels = array(
        'name'               => __('Malls', 'mallfinder'),
        'singular_name'      => __('Mall', 'mallfinder'),
        'menu_name'          => __('Malls', 'mallfinder'),
        'add_new'            => __('Add New Mall', 'mallfinder'),
        'add_new_item'       => __('Add New Mall', 'mallfinder'),
        'edit_item'          => __('Edit Mall', 'mallfinder'),
        'new_item'           => __('New Mall', 'mallfinder'),
        'view_item'          => __('View Mall', 'mallfinder'),
        'search_items'       => __('Search Malls', 'mallfinder'),
        'not_found'          => __('No malls found', 'mallfinder'),
        'not_found_in_trash' => __('No malls found in trash', 'mallfinder'),
    );
    
    $mall_args = array(
        'labels'             => $mall_labels,
        'public'             => true,
        'has_archive'        => true,
        'publicly_queryable' => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'malls', 'with_front' => false),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-building',
        'show_in_rest'       => false,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'show_in_admin_bar'  => true,
    );
    
    register_post_type('mall', $mall_args);
    
    // Store Post Type
    $store_labels = array(
        'name'               => __('Stores', 'mallfinder'),
        'singular_name'      => __('Store', 'mallfinder'),
        'menu_name'          => __('Stores', 'mallfinder'),
        'add_new'            => __('Add New Store', 'mallfinder'),
        'add_new_item'       => __('Add New Store', 'mallfinder'),
        'edit_item'          => __('Edit Store', 'mallfinder'),
        'new_item'           => __('New Store', 'mallfinder'),
        'view_item'          => __('View Store', 'mallfinder'),
        'search_items'       => __('Search Stores', 'mallfinder'),
        'not_found'          => __('No stores found', 'mallfinder'),
        'not_found_in_trash' => __('No stores found in trash', 'mallfinder'),
        'parent_item_colon'  => __('Parent Mall:', 'mallfinder'),
    );
    
    $store_args = array(
        'labels'             => $store_labels,
        'public'             => true,
        'has_archive'        => true,
        'publicly_queryable' => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'stores', 'with_front' => false),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-store',
        'show_in_rest'       => false,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => true,
        'show_in_admin_bar'  => true,
    );
    
    register_post_type('store', $store_args);
    
    // Advertisement Post Type
    $ad_labels = array(
        'name'               => __('Advertisements', 'mallfinder'),
        'singular_name'      => __('Advertisement', 'mallfinder'),
        'menu_name'          => __('Advertisements', 'mallfinder'),
        'add_new'            => __('Add New Ad', 'mallfinder'),
        'add_new_item'       => __('Add New Advertisement', 'mallfinder'),
        'edit_item'          => __('Edit Advertisement', 'mallfinder'),
        'new_item'           => __('New Advertisement', 'mallfinder'),
        'view_item'          => __('View Advertisement', 'mallfinder'),
        'search_items'       => __('Search Advertisements', 'mallfinder'),
        'not_found'          => __('No advertisements found', 'mallfinder'),
        'not_found_in_trash' => __('No advertisements found in trash', 'mallfinder'),
    );
    
    $ad_args = array(
        'labels'             => $ad_labels,
        'public'             => false,
        'has_archive'        => false,
        'publicly_queryable' => false,
        'query_var'          => false,
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'supports'           => array('title', 'custom-fields'),
        'menu_position'      => 7,
        'menu_icon'          => 'dashicons-megaphone',
        'show_in_rest'       => false,
        'show_in_menu'       => true,
        'show_in_nav_menus'  => false,
        'show_in_admin_bar'  => false,
    );
    
    register_post_type('advertisement', $ad_args);
}
add_action('init', 'mallfinder_register_post_types');

/**
 * Add Custom Columns to Mall Admin
 */
function mallfinder_mall_columns($columns) {
    $new_columns = array(
        'cb'        => $columns['cb'],
        'thumbnail' => __('Image', 'mallfinder'),
        'title'     => __('Mall Name', 'mallfinder'),
        'status'    => __('Status', 'mallfinder'),
        'location'  => __('Location', 'mallfinder'),
        'stores'    => __('Stores', 'mallfinder'),
        'date'      => $columns['date'],
    );
    return $new_columns;
}
add_filter('manage_mall_posts_columns', 'mallfinder_mall_columns');

/**
 * Render Custom Columns for Mall
 */
function mallfinder_mal_column_content($column, $post_id) {
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50));
            } else {
                echo '<span style="width:50px;height:50px;background:#e5e7eb;display:block;border-radius:4px;"></span>';
            }
            break;
            
        case 'status':
            $status = get_post_meta($post_id, '_mall_status', true);
            $status_class = $status === 'upcoming' ? 'style="color:#d97706;font-weight:600;"' : 'style="color:#059669;font-weight:600;"';
            echo '<span ' . $status_class . '>' . esc_html(ucfirst($status ?: 'Existing')) . '</span>';
            break;
            
        case 'location':
            $terms = wp_get_post_terms($post_id, array('state', 'city', 'area'));
            $locations = array();
            foreach ($terms as $term) {
                $locations[$term->taxonomy] = $term->name;
            }
            echo esc_html(implode(', ', array_filter(array(
                $locations['area'] ?? '',
                $locations['city'] ?? '',
                $locations['state'] ?? ''
            ))));
            break;
            
        case 'stores':
            $count = mallfinder_get_store_count($post_id);
            echo '<a href="' . admin_url('edit.php?post_type=store&mall_id=' . $post_id) . '">' . intval($count) . '</a>';
            break;
    }
}
add_action('manage_mall_posts_custom_column', 'mallfinder_mal_column_content', 10, 2);

/**
 * Add Custom Columns to Store Admin
 */
function mallfinder_store_columns($columns) {
    $new_columns = array(
        'cb'        => $columns['cb'],
        'thumbnail' => __('Image', 'mallfinder'),
        'title'     => __('Store Name', 'mallfinder'),
        'mall'      => __('Mall', 'mallfinder'),
        'category'  => __('Category', 'mallfinder'),
        'floor'     => __('Floor', 'mallfinder'),
        'status'    => __('Status', 'mallfinder'),
        'date'      => $columns['date'],
    );
    return $new_columns;
}
add_filter('manage_store_posts_columns', 'mallfinder_store_columns');

/**
 * Render Custom Columns for Store
 */
function mallfinder_store_column_content($column, $post_id) {
    switch ($column) {
        case 'thumbnail':
            if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, array(50, 50));
            } else {
                echo '<span style="width:50px;height:50px;background:#e5e7eb;display:block;border-radius:4px;"></span>';
            }
            break;
            
        case 'mall':
            $mall_id = get_post_meta($post_id, '_store_mall_id', true);
            if ($mall_id) {
                $mall = get_post($mall_id);
                if ($mall) {
                    echo '<a href="' . get_edit_post_link($mall_id) . '">' . esc_html($mall->post_title) . '</a>';
                }
            }
            break;
            
        case 'category':
            $categories = wp_get_post_terms($post_id, 'store_category');
            if (!empty($categories) && !is_wp_error($categories)) {
                echo esc_html($categories[0]->name);
            }
            break;
            
        case 'floor':
            echo esc_html(get_post_meta($post_id, '_store_floor', true));
            break;
            
        case 'status':
            $status = get_post_meta($post_id, '_store_status', true);
            $status_labels = array(
                'open'        => __('Open', 'mallfinder'),
                'coming_soon' => __('Coming Soon', 'mallfinder'),
                'closed'      => __('Closed', 'mallfinder'),
            );
            $status_class = $status === 'open' ? 'color:#059669;' : ($status === 'closed' ? 'color:#dc2626;' : 'color:#d97706;');
            echo '<span style="' . esc_attr($status_class) . 'font-weight:600;">' . esc_html($status_labels[$status] ?? 'Open') . '</span>';
            break;
    }
}
add_action('manage_store_posts_custom_column', 'mallfinder_store_column_content', 10, 2);

/**
 * Add Filter Dropdowns to Mall Admin
 */
function mallfinder_mal_filters() {
    global $typenow;
    
    if ($typenow === 'mall') {
        // Status filter
        $current_status = isset($_GET['mall_status']) ? $_GET['mall_status'] : '';
        echo '<select name="mall_status">';
        echo '<option value="">' . __('All Statuses', 'mallfinder') . '</option>';
        echo '<option value="existing" ' . selected($current_status, 'existing', false) . '>' . __('Existing', 'mallfinder') . '</option>';
        echo '<option value="upcoming" ' . selected($current_status, 'upcoming', false) . '>' . __('Upcoming', 'mallfinder') . '</option>';
        echo '</select>';
        
        // State filter
        $current_state = isset($_GET['state']) ? $_GET['state'] : '';
        $states = get_terms(array('taxonomy' => 'state', 'hide_empty' => false));
        if (!empty($states) && !is_wp_error($states)) {
            echo '<select name="state">';
            echo '<option value="">' . __('All States', 'mallfinder') . '</option>';
            foreach ($states as $state) {
                echo '<option value="' . esc_attr($state->slug) . '" ' . selected($current_state, $state->slug, false) . '>' . esc_html($state->name) . '</option>';
            }
            echo '</select>';
        }
    }
    
    if ($typenow === 'store') {
        // Category filter
        $current_cat = isset($_GET['store_category']) ? $_GET['store_category'] : '';
        $categories = get_terms(array('taxonomy' => 'store_category', 'hide_empty' => false));
        if (!empty($categories) && !is_wp_error($categories)) {
            echo '<select name="store_category">';
            echo '<option value="">' . __('All Categories', 'mallfinder') . '</option>';
            foreach ($categories as $cat) {
                echo '<option value="' . esc_attr($cat->slug) . '" ' . selected($current_cat, $cat->slug, false) . '>' . esc_html($cat->name) . '</option>';
            }
            echo '</select>';
        }
    }
}
add_action('restrict_manage_posts', 'mallfinder_mal_filters');

/**
 * Filter Mall Query in Admin
 */
function mallfilter_mall_admin_query($query) {
    global $pagenow, $typenow;
    
    if ($pagenow === 'edit.php' && $typenow === 'mall' && $query->is_main_query()) {
        $meta_query = array();
        
        if (isset($_GET['mall_status']) && $_GET['mall_status']) {
            $meta_query[] = array(
                'key'     => '_mall_status',
                'value'   => sanitize_text_field($_GET['mall_status']),
                'compare' => '=',
            );
        }
        
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
        
        if (isset($_GET['state']) && $_GET['state']) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'state',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['state']),
                ),
            ));
        }
    }
    
    if ($pagenow === 'edit.php' && $typenow === 'store' && $query->is_main_query()) {
        if (isset($_GET['store_category']) && $_GET['store_category']) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'store_category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field($_GET['store_category']),
                ),
            ));
        }
        
        // Filter by mall
        if (isset($_GET['mall_id']) && $_GET['mall_id']) {
            $query->set('meta_query', array(
                array(
                    'key'     => '_store_mall_id',
                    'value'   => intval($_GET['mall_id']),
                    'compare' => '=',
                ),
            ));
        }
    }
}
add_action('pre_get_posts', 'mallfilter_mall_admin_query');

<?php
/**
 * Custom Post Types Registration
 *
 * @package MallFinder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Mall Post Type
 */
function mallfinder_register_mall_post_type() {
    $labels = array(
        'name'                  => _x('Malls', 'Post Type General Name', 'mallfinder'),
        'singular_name'         => _x('Mall', 'Post Type Singular Name', 'mallfinder'),
        'menu_name'             => __('Malls', 'mallfinder'),
        'name_admin_bar'        => __('Mall', 'mallfinder'),
        'archives'              => __('Mall Archives', 'mallfinder'),
        'attributes'            => __('Mall Attributes', 'mallfinder'),
        'parent_item_colon'     => __('Parent Mall:', 'mallfinder'),
        'all_items'             => __('All Malls', 'mallfinder'),
        'add_new_item'          => __('Add New Mall', 'mallfinder'),
        'add_new'               => __('Add New', 'mallfinder'),
        'new_item'              => __('New Mall', 'mallfinder'),
        'edit_item'             => __('Edit Mall', 'mallfinder'),
        'update_item'           => __('Update Mall', 'mallfinder'),
        'view_item'             => __('View Mall', 'mallfinder'),
        'view_items'            => __('View Malls', 'mallfinder'),
        'search_items'          => __('Search Mall', 'mallfinder'),
        'not_found'             => __('Not found', 'mallfinder'),
        'not_found_in_trash'    => __('Not found in Trash', 'mallfinder'),
        'featured_image'        => __('Mall Image', 'mallfinder'),
        'set_featured_image'    => __('Set mall image', 'mallfinder'),
        'remove_featured_image' => __('Remove mall image', 'mallfinder'),
        'use_featured_image'    => __('Use as mall image', 'mallfinder'),
        'insert_into_item'      => __('Insert into mall', 'mallfinder'),
        'uploaded_to_this_item' => __('Uploaded to this mall', 'mallfinder'),
        'items_list'            => __('Malls list', 'mallfinder'),
        'items_list_navigation' => __('Malls list navigation', 'mallfinder'),
        'filter_items_list'     => __('Filter malls list', 'mallfinder'),
    );

    $args = array(
        'label'               => __('Mall', 'mallfinder'),
        'description'         => __('Shopping malls directory', 'mallfinder'),
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-store',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'rewrite'             => array(
            'slug'       => 'malls',
            'with_front' => false,
        ),
    );

    register_post_type('mall', $args);
}
add_action('init', 'mallfinder_register_mall_post_type', 0);

/**
 * Register Store Post Type
 */
function mallfinder_register_store_post_type() {
    $labels = array(
        'name'                  => _x('Stores', 'Post Type General Name', 'mallfinder'),
        'singular_name'         => _x('Store', 'Post Type Singular Name', 'mallfinder'),
        'menu_name'             => __('Stores', 'mallfinder'),
        'name_admin_bar'        => __('Store', 'mallfinder'),
        'archives'              => __('Store Archives', 'mallfinder'),
        'attributes'            => __('Store Attributes', 'mallfinder'),
        'parent_item_colon'     => __('Parent Store:', 'mallfinder'),
        'all_items'             => __('All Stores', 'mallfinder'),
        'add_new_item'          => __('Add New Store', 'mallfinder'),
        'add_new'               => __('Add New', 'mallfinder'),
        'new_item'              => __('New Store', 'mallfinder'),
        'edit_item'             => __('Edit Store', 'mallfinder'),
        'update_item'           => __('Update Store', 'mallfinder'),
        'view_item'             => __('View Store', 'mallfinder'),
        'view_items'            => __('View Stores', 'mallfinder'),
        'search_items'          => __('Search Store', 'mallfinder'),
        'not_found'             => __('Not found', 'mallfinder'),
        'not_found_in_trash'    => __('Not found in Trash', 'mallfinder'),
        'featured_image'        => __('Store Logo', 'mallfinder'),
        'set_featured_image'    => __('Set store logo', 'mallfinder'),
        'remove_featured_image' => __('Remove store logo', 'mallfinder'),
        'use_featured_image'    => __('Use as store logo', 'mallfinder'),
        'insert_into_item'      => __('Insert into store', 'mallfinder'),
        'uploaded_to_this_item' => __('Uploaded to this store', 'mallfinder'),
        'items_list'            => __('Stores list', 'mallfinder'),
        'items_list_navigation' => __('Stores list navigation', 'mallfinder'),
        'filter_items_list'     => __('Filter stores list', 'mallfinder'),
    );

    $args = array(
        'label'               => __('Store', 'mallfinder'),
        'description'         => __('Store listings within malls', 'mallfinder'),
        'labels'              => $labels,
        'supports'            => array('title', 'editor', 'thumbnail'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-cart',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'rewrite'             => array(
            'slug'       => 'stores',
            'with_front' => false,
        ),
    );

    register_post_type('store', $args);
}
add_action('init', 'mallfinder_register_store_post_type', 0);

/**
 * Register State Post Type
 */
function mallfinder_register_state_post_type() {
    $labels = array(
        'name'                  => _x('States', 'Post Type General Name', 'mallfinder'),
        'singular_name'         => _x('State', 'Post Type Singular Name', 'mallfinder'),
        'menu_name'             => __('States', 'mallfinder'),
        'name_admin_bar'        => __('State', 'mallfinder'),
        'archives'              => __('State Archives', 'mallfinder'),
        'attributes'            => __('State Attributes', 'mallfinder'),
        'parent_item_colon'     => __('Parent State:', 'mallfinder'),
        'all_items'             => __('All States', 'mallfinder'),
        'add_new_item'          => __('Add New State', 'mallfinder'),
        'add_new'               => __('Add New', 'mallfinder'),
        'new_item'              => __('New State', 'mallfinder'),
        'edit_item'             => __('Edit State', 'mallfinder'),
        'update_item'           => __('Update State', 'mallfinder'),
        'view_item'             => __('View State', 'mallfinder'),
        'view_items'            => __('View States', 'mallfinder'),
        'search_items'          => __('Search State', 'mallfinder'),
        'not_found'             => __('Not found', 'mallfinder'),
        'not_found_in_trash'    => __('Not found in Trash', 'mallfinder'),
        'items_list'            => __('States list', 'mallfinder'),
        'items_list_navigation' => __('States list navigation', 'mallfinder'),
        'filter_items_list'     => __('Filter states list', 'mallfinder'),
    );

    $args = array(
        'label'               => __('State', 'mallfinder'),
        'description'         => __('State/Province listings', 'mallfinder'),
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => 'edit.php?post_type=mall',
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'rewrite'             => array(
            'slug'       => 'states',
            'with_front' => false,
        ),
    );

    register_post_type('state', $args);
}
add_action('init', 'mallfinder_register_state_post_type', 0);

/**
 * Register City Post Type
 */
function mallfinder_register_city_post_type() {
    $labels = array(
        'name'                  => _x('Cities', 'Post Type General Name', 'mallfinder'),
        'singular_name'         => _x('City', 'Post Type Singular Name', 'mallfinder'),
        'menu_name'             => __('Cities', 'mallfinder'),
        'name_admin_bar'        => __('City', 'mallfinder'),
        'archives'              => __('City Archives', 'mallfinder'),
        'attributes'            => __('City Attributes', 'mallfinder'),
        'parent_item_colon'     => __('Parent City:', 'mallfinder'),
        'all_items'             => __('All Cities', 'mallfinder'),
        'add_new_item'          => __('Add New City', 'mallfinder'),
        'add_new'               => __('Add New', 'mallfinder'),
        'new_item'              => __('New City', 'mallfinder'),
        'edit_item'             => __('Edit City', 'mallfinder'),
        'update_item'           => __('Update City', 'mallfinder'),
        'view_item'             => __('View City', 'mallfinder'),
        'view_items'            => __('View Cities', 'mallfinder'),
        'search_items'          => __('Search City', 'mallfinder'),
        'not_found'             => __('Not found', 'mallfinder'),
        'not_found_in_trash'    => __('Not found in Trash', 'mallfinder'),
        'items_list'            => __('Cities list', 'mallfinder'),
        'items_list_navigation' => __('Cities list navigation', 'mallfinder'),
        'filter_items_list'     => __('Filter cities list', 'mallfinder'),
    );

    $args = array(
        'label'               => __('City', 'mallfinder'),
        'description'         => __('City listings', 'mallfinder'),
        'labels'              => $labels,
        'supports'            => array('title', 'thumbnail'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => 'edit.php?post_type=mall',
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'rewrite'             => array(
            'slug'       => 'cities',
            'with_front' => false,
        ),
    );

    register_post_type('city', $args);
}
add_action('init', 'mallfinder_register_city_post_type', 0);

/**
 * Register Area Post Type
 */
function mallfinder_register_area_post_type() {
    $labels = array(
        'name'                  => _x('Areas', 'Post Type General Name', 'mallfinder'),
        'singular_name'         => _x('Area', 'Post Type Singular Name', 'mallfinder'),
        'menu_name'             => __('Areas', 'mallfinder'),
        'name_admin_bar'        => __('Area', 'mallfinder'),
        'archives'              => __('Area Archives', 'mallfinder'),
        'attributes'            => __('Area Attributes', 'mallfinder'),
        'parent_item_colon'     => __('Parent Area:', 'mallfinder'),
        'all_items'             => __('All Areas', 'mallfinder'),
        'add_new_item'          => __('Add New Area', 'mallfinder'),
        'add_new'               => __('Add New', 'mallfinder'),
        'new_item'              => __('New Area', 'mallfinder'),
        'edit_item'             => __('Edit Area', 'mallfinder'),
        'update_item'           => __('Update Area', 'mallfinder'),
        'view_item'             => __('View Area', 'mallfinder'),
        'view_items'            => __('View Areas', 'mallfinder'),
        'search_items'          => __('Search Area', 'mallfinder'),
        'not_found'             => __('Not found', 'mallfinder'),
        'not_found_in_trash'    => __('Not found in Trash', 'mallfinder'),
        'items_list'            => __('Areas list', 'mallfinder'),
        'items_list_navigation' => __('Areas list navigation', 'mallfinder'),
        'filter_items_list'     => __('Filter areas list', 'mallfinder'),
    );

    $args = array(
        'label'               => __('Area', 'mallfinder'),
        'description'         => __('Area/Neighborhood listings', 'mallfinder'),
        'labels'              => $labels,
        'supports'            => array('title'),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => 'edit.php?post_type=mall',
        'show_in_admin_bar'   => false,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => true,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'rewrite'             => array(
            'slug'       => 'areas',
            'with_front' => false,
        ),
    );

    register_post_type('area', $args);
}
add_action('init', 'mallfinder_register_area_post_type', 0);

/**
 * Add Submenu Items for Location CPTs
 */
function mallfinder_add_location_submenus() {
    add_submenu_page(
        'edit.php?post_type=mall',
        __('States', 'mallfinder'),
        __('States', 'mallfinder'),
        'manage_options',
        'edit.php?post_type=state'
    );
    
    add_submenu_page(
        'edit.php?post_type=mall',
        __('Cities', 'mallfinder'),
        __('Cities', 'mallfinder'),
        'manage_options',
        'edit.php?post_type=city'
    );
    
    add_submenu_page(
        'edit.php?post_type=mall',
        __('Areas', 'mallfinder'),
        __('Areas', 'mallfinder'),
        'manage_options',
        'edit.php?post_type=area'
    );
    
    add_submenu_page(
        'edit.php?post_type=mall',
        __('Stores', 'mallfinder'),
        __('Stores', 'mallfinder'),
        'manage_options',
        'edit.php?post_type=store'
    );
}
add_action('admin_menu', 'mallfinder_add_location_submenus');

/**
 * Highlight Proper Parent Menu for Location CPTs
 */
function mallfinder_menu_highlight($parent_file) {
    global $current_screen;
    
    $location_types = array('state', 'city', 'area', 'store');
    
    if (in_array($current_screen->post_type, $location_types)) {
        $parent_file = 'edit.php?post_type=mall';
    }
    
    return $parent_file;
}
add_filter('parent_file', 'mallfinder_menu_highlight');

/**
 * Connect Taxonomies to Post Types
 * This runs after both taxonomies and post types are registered
 */
function mallfinder_connect_taxonomies() {
    // Connect mall_category taxonomy to mall post type
    if (taxonomy_exists('mall_category') && post_type_exists('mall')) {
        register_taxonomy_for_object_type('mall_category', 'mall');
    }
    
    // Connect store_category taxonomy to store post type
    if (taxonomy_exists('store_category') && post_type_exists('store')) {
        register_taxonomy_for_object_type('store_category', 'store');
    }
}
add_action('init', 'mallfinder_connect_taxonomies', 99);

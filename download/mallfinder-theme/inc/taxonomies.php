<?php
/**
 * Custom Taxonomies Registration
 *
 * @package MallFinder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Mall Category Taxonomy
 */
function mallfinder_register_mall_category_taxonomy() {
    $labels = array(
        'name'                       => _x('Mall Categories', 'Taxonomy General Name', 'mallfinder'),
        'singular_name'              => _x('Mall Category', 'Taxonomy Singular Name', 'mallfinder'),
        'menu_name'                  => __('Mall Categories', 'mallfinder'),
        'all_items'                  => __('All Categories', 'mallfinder'),
        'parent_item'                => __('Parent Category', 'mallfinder'),
        'parent_item_colon'          => __('Parent Category:', 'mallfinder'),
        'new_item_name'              => __('New Category Name', 'mallfinder'),
        'add_new_item'               => __('Add New Category', 'mallfinder'),
        'edit_item'                  => __('Edit Category', 'mallfinder'),
        'update_item'                => __('Update Category', 'mallfinder'),
        'view_item'                  => __('View Category', 'mallfinder'),
        'separate_items_with_commas' => __('Separate categories with commas', 'mallfinder'),
        'add_or_remove_items'        => __('Add or remove categories', 'mallfinder'),
        'choose_from_most_used'      => __('Choose from the most used', 'mallfinder'),
        'popular_items'              => __('Popular Categories', 'mallfinder'),
        'search_items'               => __('Search Categories', 'mallfinder'),
        'not_found'                  => __('Not Found', 'mallfinder'),
        'no_terms'                   => __('No categories', 'mallfinder'),
        'items_list'                 => __('Categories list', 'mallfinder'),
        'items_list_navigation'      => __('Categories list navigation', 'mallfinder'),
    );

    $args = array(
        'labels'             => $labels,
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_tagcloud'      => false,
        'show_in_rest'       => true,
        'rewrite'            => array(
            'slug'         => 'mall-category',
            'with_front'   => false,
            'hierarchical' => true,
        ),
    );

    register_taxonomy('mall_category', array('mall'), $args);
}
add_action('init', 'mallfinder_register_mall_category_taxonomy', 0);

/**
 * Register Store Category Taxonomy
 */
function mallfinder_register_store_category_taxonomy() {
    $labels = array(
        'name'                       => _x('Store Categories', 'Taxonomy General Name', 'mallfinder'),
        'singular_name'              => _x('Store Category', 'Taxonomy Singular Name', 'mallfinder'),
        'menu_name'                  => __('Store Categories', 'mallfinder'),
        'all_items'                  => __('All Categories', 'mallfinder'),
        'parent_item'                => __('Parent Category', 'mallfinder'),
        'parent_item_colon'          => __('Parent Category:', 'mallfinder'),
        'new_item_name'              => __('New Category Name', 'mallfinder'),
        'add_new_item'               => __('Add New Category', 'mallfinder'),
        'edit_item'                  => __('Edit Category', 'mallfinder'),
        'update_item'                => __('Update Category', 'mallfinder'),
        'view_item'                  => __('View Category', 'mallfinder'),
        'separate_items_with_commas' => __('Separate categories with commas', 'mallfinder'),
        'add_or_remove_items'        => __('Add or remove categories', 'mallfinder'),
        'choose_from_most_used'      => __('Choose from the most used', 'mallfinder'),
        'popular_items'              => __('Popular Categories', 'mallfinder'),
        'search_items'               => __('Search Categories', 'mallfinder'),
        'not_found'                  => __('Not Found', 'mallfinder'),
        'no_terms'                   => __('No categories', 'mallfinder'),
        'items_list'                 => __('Categories list', 'mallfinder'),
        'items_list_navigation'      => __('Categories list navigation', 'mallfinder'),
    );

    $args = array(
        'labels'             => $labels,
        'hierarchical'       => true,
        'public'             => true,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_nav_menus'  => true,
        'show_tagcloud'      => false,
        'show_in_rest'       => true,
        'rewrite'            => array(
            'slug'         => 'store-category',
            'with_front'   => false,
            'hierarchical' => true,
        ),
    );

    register_taxonomy('store_category', array('store'), $args);
}
add_action('init', 'mallfinder_register_store_category_taxonomy', 0);

/**
 * Default Store Categories
 */
function mallfinder_insert_default_store_categories() {
    $categories = array(
        'Fashion & Apparel'    => array('description' => 'Clothing stores, boutiques, and fashion retailers'),
        'Electronics'          => array('description' => 'Electronics, gadgets, and tech stores'),
        'Food & Dining'        => array('description' => 'Restaurants, cafes, and food courts'),
        'Home & Living'        => array('description' => 'Furniture, home decor, and household items'),
        'Beauty & Health'      => array('description' => 'Cosmetics, skincare, and wellness stores'),
        'Sports & Fitness'     => array('description' => 'Sports equipment and fitness gear'),
        'Entertainment'        => array('description' => 'Cinemas, gaming zones, and entertainment'),
        'Kids & Baby'          => array('description' => 'Children\'s clothing, toys, and baby products'),
        'Books & Stationery'   => array('description' => 'Bookstores and office supplies'),
        'Jewelry & Accessories' => array('description' => 'Jewelry, watches, and accessories'),
        'Supermarket'          => array('description' => 'Grocery stores and supermarkets'),
        'Services'             => array('description' => 'Banks, salons, and other services'),
    );
    
    foreach ($categories as $name => $data) {
        if (!term_exists($name, 'store_category')) {
            wp_insert_term($name, 'store_category', array(
                'description' => $data['description'],
                'slug'        => sanitize_title($name),
            ));
        }
    }
}
add_action('after_switch_theme', 'mallfinder_insert_default_store_categories');

/**
 * Default Mall Categories
 */
function mallfinder_insert_default_mall_categories() {
    $categories = array(
        'Shopping Mall'    => array('description' => 'Traditional shopping malls with multiple stores'),
        'Outlet Mall'      => array('description' => 'Factory outlet shopping centers'),
        'Lifestyle Center' => array('description' => 'Open-air lifestyle and retail centers'),
        'Strip Mall'       => array('description' => 'Small strip shopping centers'),
        'Department Store' => array('description' => 'Large department stores'),
    );
    
    foreach ($categories as $name => $data) {
        if (!term_exists($name, 'mall_category')) {
            wp_insert_term($name, 'mall_category', array(
                'description' => $data['description'],
                'slug'        => sanitize_title($name),
            ));
        }
    }
}
add_action('after_switch_theme', 'mallfinder_insert_default_mall_categories');

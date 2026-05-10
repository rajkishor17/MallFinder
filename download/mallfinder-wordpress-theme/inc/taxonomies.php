<?php
/**
 * Custom Taxonomies
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Taxonomies
 */
function mallfinder_register_taxonomies() {
    
    // State Taxonomy
    $state_labels = array(
        'name'              => __('States', 'mallfinder'),
        'singular_name'     => __('State', 'mallfinder'),
        'search_items'      => __('Search States', 'mallfinder'),
        'all_items'         => __('All States', 'mallfinder'),
        'edit_item'         => __('Edit State', 'mallfinder'),
        'update_item'       => __('Update State', 'mallfinder'),
        'add_new_item'      => __('Add New State', 'mallfinder'),
        'new_item_name'     => __('New State Name', 'mallfinder'),
        'menu_name'         => __('States', 'mallfinder'),
    );
    
    $state_args = array(
        'hierarchical'      => false,
        'labels'            => $state_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'state', 'with_front' => false),
        'show_in_rest'      => false,
    );
    
    register_taxonomy('state', array('mall'), $state_args);
    
    // City Taxonomy
    $city_labels = array(
        'name'              => __('Cities', 'mallfinder'),
        'singular_name'     => __('City', 'mallfinder'),
        'search_items'      => __('Search Cities', 'mallfinder'),
        'all_items'         => __('All Cities', 'mallfinder'),
        'parent_item'       => __('Parent State', 'mallfinder'),
        'parent_item_colon' => __('Parent State:', 'mallfinder'),
        'edit_item'         => __('Edit City', 'mallfinder'),
        'update_item'       => __('Update City', 'mallfinder'),
        'add_new_item'      => __('Add New City', 'mallfinder'),
        'new_item_name'     => __('New City Name', 'mallfinder'),
        'menu_name'         => __('Cities', 'mallfinder'),
    );
    
    $city_args = array(
        'hierarchical'      => true,
        'labels'            => $city_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'city', 'with_front' => false),
        'show_in_rest'      => false,
    );
    
    register_taxonomy('city', array('mall'), $city_args);
    
    // Area Taxonomy
    $area_labels = array(
        'name'              => __('Areas', 'mallfinder'),
        'singular_name'     => __('Area', 'mallfinder'),
        'search_items'      => __('Search Areas', 'mallfinder'),
        'all_items'         => __('All Areas', 'mallfinder'),
        'parent_item'       => __('Parent City', 'mallfinder'),
        'parent_item_colon' => __('Parent City:', 'mallfinder'),
        'edit_item'         => __('Edit Area', 'mallfinder'),
        'update_item'       => __('Update Area', 'mallfinder'),
        'add_new_item'      => __('Add New Area', 'mallfinder'),
        'new_item_name'     => __('New Area Name', 'mallfinder'),
        'menu_name'         => __('Areas', 'mallfinder'),
    );
    
    $area_args = array(
        'hierarchical'      => true,
        'labels'            => $area_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'area', 'with_front' => false),
        'show_in_rest'      => false,
    );
    
    register_taxonomy('area', array('mall'), $area_args);
    
    // Store Category Taxonomy
    $category_labels = array(
        'name'              => __('Store Categories', 'mallfinder'),
        'singular_name'     => __('Store Category', 'mallfinder'),
        'search_items'      => __('Search Categories', 'mallfinder'),
        'all_items'         => __('All Categories', 'mallfinder'),
        'edit_item'         => __('Edit Category', 'mallfinder'),
        'update_item'       => __('Update Category', 'mallfinder'),
        'add_new_item'      => __('Add New Category', 'mallfinder'),
        'new_item_name'     => __('New Category Name', 'mallfinder'),
        'menu_name'         => __('Store Categories', 'mallfinder'),
    );
    
    $category_args = array(
        'hierarchical'      => false,
        'labels'            => $category_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'store-category', 'with_front' => false),
        'show_in_rest'      => false,
    );
    
    register_taxonomy('store_category', array('store'), $category_args);
    
    // Amenity Taxonomy
    $amenity_labels = array(
        'name'              => __('Amenities', 'mallfinder'),
        'singular_name'     => __('Amenity', 'mallfinder'),
        'search_items'      => __('Search Amenities', 'mallfinder'),
        'all_items'         => __('All Amenities', 'mallfinder'),
        'edit_item'         => __('Edit Amenity', 'mallfinder'),
        'update_item'       => __('Update Amenity', 'mallfinder'),
        'add_new_item'      => __('Add New Amenity', 'mallfinder'),
        'new_item_name'     => __('New Amenity Name', 'mallfinder'),
        'menu_name'         => __('Amenities', 'mallfinder'),
    );
    
    $amenity_args = array(
        'hierarchical'      => false,
        'labels'            => $amenity_labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'amenity', 'with_front' => false),
        'show_in_rest'      => false,
    );
    
    register_taxonomy('amenity', array('mall'), $amenity_args);
    
    // Feature Taxonomy
    $feature_labels = array(
        'name'              => __('Features', 'mallfinder'),
        'singular_name'     => __('Feature', 'mallfinder'),
        'search_items'      => __('Search Features', 'mallfinder'),
        'all_items'         => __('All Features', 'mallfinder'),
        'edit_item'         => __('Edit Feature', 'mallfinder'),
        'update_item'       => __('Update Feature', 'mallfinder'),
        'add_new_item'      => __('Add New Feature', 'mallfinder'),
        'new_item_name'     => __('New Feature Name', 'mallfinder'),
        'menu_name'         => __('Features', 'mallfinder'),
    );
    
    $feature_args = array(
        'hierarchical'      => false,
        'labels'            => $feature_labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'feature', 'with_front' => false),
        'show_in_rest'      => false,
    );
    
    register_taxonomy('feature', array('mall'), $feature_args);
}
add_action('init', 'mallfinder_register_taxonomies', 0);

/**
 * Add Custom Fields to Taxonomies
 */
// Add form fields for State
function mallfinder_state_add_form_fields() {
    ?>
    <div class="form-field">
        <label for="state_code"><?php _e('State Code', 'mallfinder'); ?></label>
        <input type="text" name="state_code" id="state_code" value="" />
        <p class="description"><?php _e('Enter the state code (e.g., NY, CA)', 'mallfinder'); ?></p>
    </div>
    <?php
}
add_action('state_add_form_fields', 'mallfinder_state_add_form_fields');

// Edit form fields for State
function mallfinder_state_edit_form_fields($term) {
    $state_code = get_term_meta($term->term_id, 'state_code', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="state_code"><?php _e('State Code', 'mallfinder'); ?></label></th>
        <td>
            <input type="text" name="state_code" id="state_code" value="<?php echo esc_attr($state_code); ?>" />
            <p class="description"><?php _e('Enter the state code (e.g., NY, CA)', 'mallfinder'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('state_edit_form_fields', 'mallfinder_state_edit_form_fields');

// Save term meta
function mallfinder_save_term_meta($term_id) {
    if (isset($_POST['state_code'])) {
        update_term_meta($term_id, 'state_code', sanitize_text_field($_POST['state_code']));
    }
}
add_action('edited_state', 'mallfinder_save_term_meta');
add_action('create_state', 'mallfinder_save_term_meta');

/**
 * Add Custom Columns to Taxonomy Admin
 */
function mallfinder_taxonomy_columns($columns) {
    $columns['count'] = __('Malls', 'mallfinder');
    return $columns;
}
add_filter('manage_edit-state_columns', 'mallfinder_taxonomy_columns');
add_filter('manage_edit-city_columns', 'mallfinder_taxonomy_columns');
add_filter('manage_edit-area_columns', 'mallfinder_taxonomy_columns');

/**
 * Get Cities by State
 */
function mallfinder_get_cities_by_state($state_id) {
    return get_terms(array(
        'taxonomy'   => 'city',
        'hide_empty' => false,
        'parent'     => $state_id,
    ));
}

/**
 * Get Areas by City
 */
function mallfinder_get_areas_by_city($city_id) {
    return get_terms(array(
        'taxonomy'   => 'area',
        'hide_empty' => false,
        'parent'     => $city_id,
    ));
}

/**
 * AJAX: Get Cities for Selected State
 */
function mallfinder_ajax_get_cities() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $state_id = isset($_GET['state_id']) ? intval($_GET['state_id']) : 0;
    
    if (!$state_id) {
        wp_send_json_error('No state ID provided');
    }
    
    $cities = mallfinder_get_cities_by_state($state_id);
    
    $data = array();
    foreach ($cities as $city) {
        $data[] = array(
            'id'   => $city->term_id,
            'name' => $city->name,
            'slug' => $city->slug,
        );
    }
    
    wp_send_json_success($data);
}
add_action('wp_ajax_get_cities', 'mallfinder_ajax_get_cities');
add_action('wp_ajax_nopriv_get_cities', 'mallfinder_ajax_get_cities');

/**
 * AJAX: Get Areas for Selected City
 */
function mallfinder_ajax_get_areas() {
    check_ajax_referer('mallfinder_nonce', 'nonce');
    
    $city_id = isset($_GET['city_id']) ? intval($_GET['city_id']) : 0;
    
    if (!$city_id) {
        wp_send_json_error('No city ID provided');
    }
    
    $areas = mallfinder_get_areas_by_city($city_id);
    
    $data = array();
    foreach ($areas as $area) {
        $data[] = array(
            'id'   => $area->term_id,
            'name' => $area->name,
            'slug' => $area->slug,
        );
    }
    
    wp_send_json_success($data);
}
add_action('wp_ajax_get_areas', 'mallfinder_ajax_get_areas');
add_action('wp_ajax_nopriv_get_areas', 'mallfinder_ajax_get_areas');

/**
 * Default Store Categories
 */
function mallfinder_insert_default_categories() {
    $categories = array(
        'Fashion & Apparel',
        'Electronics',
        'Food & Dining',
        'Entertainment',
        'Health & Beauty',
        'Home & Living',
        'Sports & Fitness',
        'Books & Stationery',
        'Jewelry & Accessories',
        'Kids & Toys',
        'Services',
        'Supermarket',
        'Cinema',
        'Gaming',
        'Banking & Finance',
    );
    
    foreach ($categories as $category) {
        if (!term_exists($category, 'store_category')) {
            wp_insert_term($category, 'store_category');
        }
    }
}
add_action('after_switch_theme', 'mallfinder_insert_default_categories');

/**
 * Default Amenities
 */
function mallfinder_insert_default_amenities() {
    $amenities = array(
        'Parking',
        'Food Court',
        'Restrooms',
        'ATM',
        'Wheelchair Accessible',
        'WiFi',
        'Valet Parking',
        'Kids Play Area',
        'Prayer Room',
        'Baby Care Room',
        'Lost & Found',
        'Information Desk',
        'Lockers',
        'Car Charging Station',
    );
    
    foreach ($amenities as $amenity) {
        if (!term_exists($amenity, 'amenity')) {
            wp_insert_term($amenity, 'amenity');
        }
    }
}
add_action('after_switch_theme', 'mallfinder_insert_default_amenities');

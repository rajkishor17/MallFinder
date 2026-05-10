<?php
/**
 * Custom Meta Boxes
 *
 * @package MallFinder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Meta Boxes
 */
function mallfinder_add_meta_boxes() {
    // Mall Meta Box
    add_meta_box(
        'mall_details',
        __('Mall Details', 'mallfinder'),
        'mallfinder_mall_meta_box_callback',
        'mall',
        'normal',
        'high'
    );
    
    // Store Meta Box
    add_meta_box(
        'store_details',
        __('Store Details', 'mallfinder'),
        'mallfinder_store_meta_box_callback',
        'store',
        'normal',
        'high'
    );
    
    // City Meta Box
    add_meta_box(
        'city_details',
        __('City Details', 'mallfinder'),
        'mallfinder_city_meta_box_callback',
        'city',
        'normal',
        'high'
    );
    
    // Area Meta Box
    add_meta_box(
        'area_details',
        __('Area Details', 'mallfinder'),
        'mallfinder_area_meta_box_callback',
        'area',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mallfinder_add_meta_boxes');

/**
 * Mall Meta Box Callback
 */
function mallfinder_mall_meta_box_callback($post) {
    wp_nonce_field('mallfinder_mall_meta', 'mallfinder_mall_meta_nonce');
    
    $values = array(
        'address'       => get_post_meta($post->ID, '_mall_address', true),
        'state'         => get_post_meta($post->ID, '_mall_state', true),
        'city'          => get_post_meta($post->ID, '_mall_city', true),
        'area'          => get_post_meta($post->ID, '_mall_area', true),
        'pincode'       => get_post_meta($post->ID, '_mall_pincode', true),
        'latitude'      => get_post_meta($post->ID, '_mall_latitude', true),
        'longitude'     => get_post_meta($post->ID, '_mall_longitude', true),
        'phone'         => get_post_meta($post->ID, '_mall_phone', true),
        'email'         => get_post_meta($post->ID, '_mall_email', true),
        'website'       => get_post_meta($post->ID, '_mall_website', true),
        'opening_hours' => get_post_meta($post->ID, '_mall_opening_hours', true),
        'total_stores'  => get_post_meta($post->ID, '_mall_total_stores', true),
        'floors'        => get_post_meta($post->ID, '_mall_floors', true),
        'parking'       => get_post_meta($post->ID, '_mall_parking', true),
    );
    ?>
    <div class="mallfinder-meta-box">
        <div class="meta-section">
            <h4><?php _e('Location Information', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="mall_state"><?php _e('State', 'mallfinder'); ?></label>
                    <?php
                    $states = get_posts(array('post_type' => 'state', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                    ?>
                    <select name="mall_state" id="mall_state" class="widefat">
                        <option value=""><?php _e('Select State', 'mallfinder'); ?></option>
                        <?php foreach ($states as $state) : ?>
                            <option value="<?php echo $state->ID; ?>" <?php selected($values['state'], $state->ID); ?>>
                                <?php echo esc_html($state->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="meta-field">
                    <label for="mall_city"><?php _e('City', 'mallfinder'); ?></label>
                    <?php
                    $cities = get_posts(array('post_type' => 'city', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                    ?>
                    <select name="mall_city" id="mall_city" class="widefat">
                        <option value=""><?php _e('Select City', 'mallfinder'); ?></option>
                        <?php foreach ($cities as $city) : ?>
                            <option value="<?php echo $city->ID; ?>" <?php selected($values['city'], $city->ID); ?>>
                                <?php echo esc_html($city->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="meta-field">
                    <label for="mall_area"><?php _e('Area', 'mallfinder'); ?></label>
                    <?php
                    $areas = get_posts(array('post_type' => 'area', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                    ?>
                    <select name="mall_area" id="mall_area" class="widefat">
                        <option value=""><?php _e('Select Area', 'mallfinder'); ?></option>
                        <?php foreach ($areas as $area) : ?>
                            <option value="<?php echo $area->ID; ?>" <?php selected($values['area'], $area->ID); ?>>
                                <?php echo esc_html($area->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="mall_address"><?php _e('Full Address', 'mallfinder'); ?></label>
                    <textarea name="mall_address" id="mall_address" class="widefat" rows="2"><?php echo esc_textarea($values['address']); ?></textarea>
                </div>
                <div class="meta-field small">
                    <label for="mall_pincode"><?php _e('PIN Code', 'mallfinder'); ?></label>
                    <input type="text" name="mall_pincode" id="mall_pincode" class="widefat" value="<?php echo esc_attr($values['pincode']); ?>">
                </div>
            </div>
        </div>
        
        <div class="meta-section">
            <h4><?php _e('Map Coordinates', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="mall_latitude"><?php _e('Latitude', 'mallfinder'); ?></label>
                    <input type="text" name="mall_latitude" id="mall_latitude" class="widefat" value="<?php echo esc_attr($values['latitude']); ?>">
                </div>
                <div class="meta-field">
                    <label for="mall_longitude"><?php _e('Longitude', 'mallfinder'); ?></label>
                    <input type="text" name="mall_longitude" id="mall_longitude" class="widefat" value="<?php echo esc_attr($values['longitude']); ?>">
                </div>
            </div>
            <p class="description"><?php _e('Enter coordinates or use the map to set location.', 'mallfinder'); ?></p>
            <div id="mall-map-picker" style="height: 300px; margin-top: 10px; border: 1px solid #ccc; border-radius: 4px;"></div>
        </div>
        
        <div class="meta-section">
            <h4><?php _e('Contact Information', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="mall_phone"><?php _e('Phone Number', 'mallfinder'); ?></label>
                    <input type="text" name="mall_phone" id="mall_phone" class="widefat" value="<?php echo esc_attr($values['phone']); ?>">
                </div>
                <div class="meta-field">
                    <label for="mall_email"><?php _e('Email Address', 'mallfinder'); ?></label>
                    <input type="email" name="mall_email" id="mall_email" class="widefat" value="<?php echo esc_attr($values['email']); ?>">
                </div>
            </div>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="mall_website"><?php _e('Website URL', 'mallfinder'); ?></label>
                    <input type="url" name="mall_website" id="mall_website" class="widefat" value="<?php echo esc_attr($values['website']); ?>">
                </div>
            </div>
        </div>
        
        <div class="meta-section">
            <h4><?php _e('Mall Information', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="mall_opening_hours"><?php _e('Opening Hours', 'mallfinder'); ?></label>
                    <textarea name="mall_opening_hours" id="mall_opening_hours" class="widefat" rows="3" placeholder="Mon-Sat: 10:00 AM - 10:00 PM&#10;Sunday: 11:00 AM - 9:00 PM"><?php echo esc_textarea($values['opening_hours']); ?></textarea>
                </div>
            </div>
            <div class="meta-row">
                <div class="meta-field small">
                    <label for="mall_total_stores"><?php _e('Total Stores', 'mallfinder'); ?></label>
                    <input type="number" name="mall_total_stores" id="mall_total_stores" class="widefat" value="<?php echo esc_attr($values['total_stores']); ?>">
                </div>
                <div class="meta-field small">
                    <label for="mall_floors"><?php _e('Number of Floors', 'mallfinder'); ?></label>
                    <input type="number" name="mall_floors" id="mall_floors" class="widefat" value="<?php echo esc_attr($values['floors']); ?>">
                </div>
                <div class="meta-field small">
                    <label for="mall_parking"><?php _e('Parking Capacity', 'mallfinder'); ?></label>
                    <input type="text" name="mall_parking" id="mall_parking" class="widefat" value="<?php echo esc_attr($values['parking']); ?>" placeholder="e.g., 500 cars">
                </div>
            </div>
        </div>
    </div>
    <style>
        .mallfinder-meta-box .meta-section {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .mallfinder-meta-box .meta-section h4 {
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .mallfinder-meta-box .meta-row {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }
        .mallfinder-meta-box .meta-field {
            flex: 1;
        }
        .mallfinder-meta-box .meta-field.small {
            flex: 0.5;
        }
        .mallfinder-meta-box .meta-field label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        @media (max-width: 768px) {
            .mallfinder-meta-box .meta-row {
                flex-direction: column;
            }
        }
    </style>
    <?php
}

/**
 * Store Meta Box Callback
 */
function mallfinder_store_meta_box_callback($post) {
    wp_nonce_field('mallfinder_store_meta', 'mallfinder_store_meta_nonce');
    
    $values = array(
        'mall'         => get_post_meta($post->ID, '_store_mall', true),
        'logo'         => get_post_meta($post->ID, '_store_logo', true),
        'phone'        => get_post_meta($post->ID, '_store_phone', true),
        'email'        => get_post_meta($post->ID, '_store_email', true),
        'website'      => get_post_meta($post->ID, '_store_website', true),
        'floor'        => get_post_meta($post->ID, '_store_floor', true),
        'unit'         => get_post_meta($post->ID, '_store_unit', true),
        'opening_hours' => get_post_meta($post->ID, '_store_opening_hours', true),
    );
    ?>
    <div class="mallfinder-meta-box">
        <div class="meta-section">
            <h4><?php _e('Store Location', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="store_mall"><?php _e('Mall', 'mallfinder'); ?> *</label>
                    <?php
                    $malls = get_posts(array('post_type' => 'mall', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                    ?>
                    <select name="store_mall" id="store_mall" class="widefat" required>
                        <option value=""><?php _e('Select Mall', 'mallfinder'); ?></option>
                        <?php foreach ($malls as $mall) : ?>
                            <option value="<?php echo $mall->ID; ?>" <?php selected($values['mall'], $mall->ID); ?>>
                                <?php echo esc_html($mall->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="meta-field small">
                    <label for="store_floor"><?php _e('Floor', 'mallfinder'); ?></label>
                    <input type="text" name="store_floor" id="store_floor" class="widefat" value="<?php echo esc_attr($values['floor']); ?>" placeholder="e.g., Ground Floor">
                </div>
                <div class="meta-field small">
                    <label for="store_unit"><?php _e('Unit/Shop No.', 'mallfinder'); ?></label>
                    <input type="text" name="store_unit" id="store_unit" class="widefat" value="<?php echo esc_attr($values['unit']); ?>" placeholder="e.g., G-12">
                </div>
            </div>
        </div>
        
        <div class="meta-section">
            <h4><?php _e('Store Logo', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="store_logo"><?php _e('Logo Image', 'mallfinder'); ?></label>
                    <div class="logo-upload-wrapper">
                        <input type="hidden" name="store_logo" id="store_logo" value="<?php echo esc_attr($values['logo']); ?>">
                        <button type="button" class="button upload-logo-btn"><?php _e('Upload Logo', 'mallfinder'); ?></button>
                        <button type="button" class="button remove-logo-btn" style="<?php echo $values['logo'] ? '' : 'display:none;'; ?>"><?php _e('Remove', 'mallfinder'); ?></button>
                        <div class="logo-preview" style="margin-top: 10px;">
                            <?php if ($values['logo']) : ?>
                                <img src="<?php echo wp_get_attachment_url($values['logo']); ?>" style="max-width: 150px; height: auto;">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="meta-section">
            <h4><?php _e('Contact Information', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="store_phone"><?php _e('Phone Number', 'mallfinder'); ?></label>
                    <input type="text" name="store_phone" id="store_phone" class="widefat" value="<?php echo esc_attr($values['phone']); ?>">
                </div>
                <div class="meta-field">
                    <label for="store_email"><?php _e('Email Address', 'mallfinder'); ?></label>
                    <input type="email" name="store_email" id="store_email" class="widefat" value="<?php echo esc_attr($values['email']); ?>">
                </div>
            </div>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="store_website"><?php _e('Website URL', 'mallfinder'); ?></label>
                    <input type="url" name="store_website" id="store_website" class="widefat" value="<?php echo esc_attr($values['website']); ?>">
                </div>
            </div>
        </div>
        
        <div class="meta-section">
            <h4><?php _e('Operating Hours', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="store_opening_hours"><?php _e('Store Hours', 'mallfinder'); ?></label>
                    <textarea name="store_opening_hours" id="store_opening_hours" class="widefat" rows="3" placeholder="Mon-Sat: 10:00 AM - 9:00 PM&#10;Sunday: Closed"><?php echo esc_textarea($values['opening_hours']); ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * City Meta Box Callback
 */
function mallfinder_city_meta_box_callback($post) {
    wp_nonce_field('mallfinder_city_meta', 'mallfinder_city_meta_nonce');
    
    $state = get_post_meta($post->ID, '_city_state', true);
    ?>
    <div class="mallfinder-meta-box">
        <div class="meta-section">
            <h4><?php _e('City Information', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="city_state"><?php _e('State', 'mallfinder'); ?> *</label>
                    <?php
                    $states = get_posts(array('post_type' => 'state', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                    ?>
                    <select name="city_state" id="city_state" class="widefat" required>
                        <option value=""><?php _e('Select State', 'mallfinder'); ?></option>
                        <?php foreach ($states as $s) : ?>
                            <option value="<?php echo $s->ID; ?>" <?php selected($state, $s->ID); ?>>
                                <?php echo esc_html($s->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Area Meta Box Callback
 */
function mallfinder_area_meta_box_callback($post) {
    wp_nonce_field('mallfinder_area_meta', 'mallfinder_area_meta_nonce');
    
    $city = get_post_meta($post->ID, '_area_city', true);
    ?>
    <div class="mallfinder-meta-box">
        <div class="meta-section">
            <h4><?php _e('Area Information', 'mallfinder'); ?></h4>
            <div class="meta-row">
                <div class="meta-field">
                    <label for="area_city"><?php _e('City', 'mallfinder'); ?> *</label>
                    <?php
                    $cities = get_posts(array('post_type' => 'city', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
                    ?>
                    <select name="area_city" id="area_city" class="widefat" required>
                        <option value=""><?php _e('Select City', 'mallfinder'); ?></option>
                        <?php foreach ($cities as $c) : ?>
                            <option value="<?php echo $c->ID; ?>" <?php selected($city, $c->ID); ?>>
                                <?php echo esc_html($c->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Save Meta Boxes
 */
function mallfinder_save_meta_boxes($post_id) {
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save Mall Meta
    if (isset($_POST['mallfinder_mall_meta_nonce']) && wp_verify_nonce($_POST['mallfinder_mall_meta_nonce'], 'mallfinder_mall_meta')) {
        $mall_fields = array(
            'mall_address', 'mall_state', 'mall_city', 'mall_area', 'mall_pincode',
            'mall_latitude', 'mall_longitude', 'mall_phone', 'mall_email', 'mall_website',
            'mall_opening_hours', 'mall_total_stores', 'mall_floors', 'mall_parking'
        );
        
        foreach ($mall_fields as $field) {
            if (isset($_POST[$field])) {
                $meta_key = '_' . $field;
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $meta_key, $value);
            }
        }
    }
    
    // Save Store Meta
    if (isset($_POST['mallfinder_store_meta_nonce']) && wp_verify_nonce($_POST['mallfinder_store_meta_nonce'], 'mallfinder_store_meta')) {
        $store_fields = array(
            'store_mall', 'store_logo', 'store_phone', 'store_email', 'store_website',
            'store_floor', 'store_unit', 'store_opening_hours'
        );
        
        foreach ($store_fields as $field) {
            if (isset($_POST[$field])) {
                $meta_key = '_' . $field;
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $meta_key, $value);
            }
        }
    }
    
    // Save City Meta
    if (isset($_POST['mallfinder_city_meta_nonce']) && wp_verify_nonce($_POST['mallfinder_city_meta_nonce'], 'mallfinder_city_meta')) {
        if (isset($_POST['city_state'])) {
            update_post_meta($post_id, '_city_state', sanitize_text_field($_POST['city_state']));
        }
    }
    
    // Save Area Meta
    if (isset($_POST['mallfinder_area_meta_nonce']) && wp_verify_nonce($_POST['mallfinder_area_meta_nonce'], 'mallfinder_area_meta')) {
        if (isset($_POST['area_city'])) {
            update_post_meta($post_id, '_area_city', sanitize_text_field($_POST['area_city']));
        }
    }
}
add_action('save_post', 'mallfinder_save_meta_boxes');

/**
 * Enqueue Admin Scripts for Meta Boxes
 */
function mallfinder_admin_scripts($hook) {
    global $post_type;
    
    $version = defined('MALLFINDER_VERSION') ? MALLFINDER_VERSION : '2.1.0';
    
    if (($hook == 'post-new.php' || $hook == 'post.php') && $post_type == 'mall') {
        wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css', array(), '1.9.4');
        wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), '1.9.4', true);
        wp_enqueue_script('mallfinder-admin-map', get_template_directory_uri() . '/assets/js/admin-map.js', array('leaflet-js'), $version, true);
        
        wp_localize_script('mallfinder-admin-map', 'mallfinderAdmin', array(
            'defaultLat' => get_option('mallfinder_default_lat', '40.7128'),
            'defaultLng' => get_option('mallfinder_default_lng', '-74.0060'),
        ));
    }
    
    // Media uploader for store logo
    if (($hook == 'post-new.php' || $hook == 'post.php') && $post_type == 'store') {
        wp_enqueue_media();
        wp_enqueue_script('mallfinder-admin-media', get_template_directory_uri() . '/assets/js/admin-media.js', array('jquery'), $version, true);
    }
}
add_action('admin_enqueue_scripts', 'mallfinder_admin_scripts');

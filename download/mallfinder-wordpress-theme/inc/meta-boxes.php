<?php
/**
 * Meta Boxes for Custom Post Types
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Meta Boxes
 */
function mallfinder_add_meta_boxes() {
    // Mall Details Meta Box
    add_meta_box(
        'mall_details',
        __('Mall Details', 'mallfinder'),
        'mallfinder_mall_details_callback',
        'mall',
        'normal',
        'high'
    );
    
    // Mall Location Meta Box
    add_meta_box(
        'mall_location',
        __('Location & Map', 'mallfinder'),
        'mallfinder_mall_location_callback',
        'mall',
        'normal',
        'high'
    );
    
    // Mall SEO Meta Box
    add_meta_box(
        'mall_seo',
        __('SEO Settings', 'mallfinder'),
        'mallfinder_mall_seo_callback',
        'mall',
        'normal',
        'low'
    );
    
    // Store Details Meta Box
    add_meta_box(
        'store_details',
        __('Store Details', 'mallfinder'),
        'mallfinder_store_details_callback',
        'store',
        'normal',
        'high'
    );
    
    // Advertisement Details Meta Box
    add_meta_box(
        'ad_details',
        __('Advertisement Settings', 'mallfinder'),
        'mallfinder_ad_details_callback',
        'advertisement',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'mallfinder_add_meta_boxes');

/**
 * Mall Details Meta Box Callback
 */
function mallfinder_mall_details_callback($post) {
    wp_nonce_field('mallfinder_mall_details', 'mallfinder_mall_details_nonce');
    
    $status = get_post_meta($post->ID, '_mall_status', true);
    $address = get_post_meta($post->ID, '_mall_address', true);
    $phone = get_post_meta($post->ID, '_mall_phone', true);
    $website = get_post_meta($post->ID, '_mall_website', true);
    $opening_hours = get_post_meta($post->ID, '_mall_opening_hours', true);
    $expected_opening = get_post_meta($post->ID, '_mall_expected_opening', true);
    $stores_count = get_post_meta($post->ID, '_mall_stores_count', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="mall_status"><?php _e('Status', 'mallfinder'); ?></label></th>
            <td>
                <select name="mall_status" id="mall_status">
                    <option value="existing" <?php selected($status, 'existing'); ?>><?php _e('Existing', 'mallfinder'); ?></option>
                    <option value="upcoming" <?php selected($status, 'upcoming'); ?>><?php _e('Upcoming', 'mallfinder'); ?></option>
                </select>
                <p class="description"><?php _e('Select whether this mall is currently operating or upcoming.', 'mallfinder'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_address"><?php _e('Address', 'mallfinder'); ?></label></th>
            <td>
                <textarea name="mall_address" id="mall_address" rows="2" class="large-text"><?php echo esc_textarea($address); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_phone"><?php _e('Phone', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="mall_phone" id="mall_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_website"><?php _e('Website', 'mallfinder'); ?></label></th>
            <td>
                <input type="url" name="mall_website" id="mall_website" value="<?php echo esc_attr($website); ?>" class="regular-text" placeholder="https://" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_opening_hours"><?php _e('Opening Hours', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="mall_opening_hours" id="mall_opening_hours" value="<?php echo esc_attr($opening_hours); ?>" class="regular-text" placeholder="Mon-Sun: 10:00 AM - 10:00 PM" />
            </td>
        </tr>
        <tr class="upcoming-field" style="<?php echo $status !== 'upcoming' ? 'display:none;' : ''; ?>">
            <th scope="row"><label for="mall_expected_opening"><?php _e('Expected Opening Date', 'mallfinder'); ?></label></th>
            <td>
                <input type="date" name="mall_expected_opening" id="mall_expected_opening" value="<?php echo esc_attr($expected_opening); ?>" class="regular-text" />
                <p class="description"><?php _e('For upcoming malls only.', 'mallfinder'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_stores_count"><?php _e('Number of Stores', 'mallfinder'); ?></label></th>
            <td>
                <input type="number" name="mall_stores_count" id="mall_stores_count" value="<?php echo esc_attr($stores_count); ?>" class="small-text" min="0" />
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        $('#mall_status').change(function() {
            if ($(this).val() === 'upcoming') {
                $('.upcoming-field').show();
            } else {
                $('.upcoming-field').hide();
            }
        });
    });
    </script>
    <?php
}

/**
 * Mall Location Meta Box Callback
 */
function mallfinder_mall_location_callback($post) {
    $latitude = get_post_meta($post->ID, '_mall_latitude', true);
    $longitude = get_post_meta($post->ID, '_mall_longitude', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="mall_latitude"><?php _e('Latitude', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="mall_latitude" id="mall_latitude" value="<?php echo esc_attr($latitude); ?>" class="regular-text" placeholder="e.g., 28.6139" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_longitude"><?php _e('Longitude', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="mall_longitude" id="mall_longitude" value="<?php echo esc_attr($longitude); ?>" class="regular-text" placeholder="e.g., 77.2090" />
            </td>
        </tr>
    </table>
    
    <div id="mall-map-picker" style="height: 400px; margin-top: 10px; border: 1px solid #ccc; border-radius: 4px;"></div>
    
    <p class="description"><?php _e('Click on the map to set the location, or enter coordinates manually.', 'mallfinder'); ?></p>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    jQuery(document).ready(function($) {
        var lat = parseFloat($('#mall_latitude').val()) || 20.5937;
        var lng = parseFloat($('#mall_longitude').val()) || 78.9629;
        
        var map = L.map('mall-map-picker').setView([lat, lng], 5);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        
        var marker = L.marker([lat, lng], {draggable: true}).addTo(map);
        
        marker.on('dragend', function(e) {
            var pos = e.target.getLatLng();
            $('#mall_latitude').val(pos.lat.toFixed(6));
            $('#mall_longitude').val(pos.lng.toFixed(6));
        });
        
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            $('#mall_latitude').val(e.latlng.lat.toFixed(6));
            $('#mall_longitude').val(e.latlng.lng.toFixed(6));
        });
        
        // Update map when coordinates are changed manually
        $('#mall_latitude, #mall_longitude').change(function() {
            var newLat = parseFloat($('#mall_latitude').val());
            var newLng = parseFloat($('#mall_longitude').val());
            if (newLat && newLng) {
                marker.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng], 12);
            }
        });
    });
    </script>
    <?php
}

/**
 * Mall SEO Meta Box Callback
 */
function mallfinder_mall_seo_callback($post) {
    wp_nonce_field('mallfinder_mall_seo', 'mallfinder_mall_seo_nonce');
    
    $meta_title = get_post_meta($post->ID, '_mall_meta_title', true);
    $meta_description = get_post_meta($post->ID, '_mall_meta_description', true);
    $keywords = get_post_meta($post->ID, '_mall_keywords', true);
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="mall_meta_title"><?php _e('Meta Title', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="mall_meta_title" id="mall_meta_title" value="<?php echo esc_attr($meta_title); ?>" class="large-text" />
                <p class="description"><?php _e('Custom title for search engines. Leave blank to use the mall name.', 'mallfinder'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_meta_description"><?php _e('Meta Description', 'mallfinder'); ?></label></th>
            <td>
                <textarea name="mall_meta_description" id="mall_meta_description" rows="3" class="large-text"><?php echo esc_textarea($meta_description); ?></textarea>
                <p class="description"><?php _e('Brief description for search engines (recommended: 150-160 characters).', 'mallfinder'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="mall_keywords"><?php _e('Keywords', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="mall_keywords" id="mall_keywords" value="<?php echo esc_attr($keywords); ?>" class="large-text" placeholder="shopping, mall, stores, restaurants" />
                <p class="description"><?php _e('Comma-separated keywords.', 'mallfinder'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Store Details Meta Box Callback
 */
function mallfinder_store_details_callback($post) {
    wp_nonce_field('mallfinder_store_details', 'mallfinder_store_details_nonce');
    
    $mall_id = get_post_meta($post->ID, '_store_mall_id', true);
    $category = get_post_meta($post->ID, '_store_category', true);
    $floor = get_post_meta($post->ID, '_store_floor', true);
    $unit = get_post_meta($post->ID, '_store_unit', true);
    $phone = get_post_meta($post->ID, '_store_phone', true);
    $website = get_post_meta($post->ID, '_store_website', true);
    $status = get_post_meta($post->ID, '_store_status', true);
    $opening_hours = get_post_meta($post->ID, '_store_opening_hours', true);
    
    // Get all malls
    $malls = get_posts(array(
        'post_type'      => 'mall',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    
    // Get store categories
    $categories = get_terms(array(
        'taxonomy'   => 'store_category',
        'hide_empty' => false,
        'orderby'    => 'name',
    ));
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="store_mall_id"><?php _e('Mall', 'mallfinder'); ?></label></th>
            <td>
                <select name="store_mall_id" id="store_mall_id" required>
                    <option value=""><?php _e('Select a Mall', 'mallfinder'); ?></option>
                    <?php foreach ($malls as $mall) : ?>
                        <option value="<?php echo esc_attr($mall->ID); ?>" <?php selected($mall_id, $mall->ID); ?>>
                            <?php echo esc_html($mall->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="store_status"><?php _e('Status', 'mallfinder'); ?></label></th>
            <td>
                <select name="store_status" id="store_status">
                    <option value="open" <?php selected($status, 'open'); ?>><?php _e('Open', 'mallfinder'); ?></option>
                    <option value="coming_soon" <?php selected($status, 'coming_soon'); ?>><?php _e('Coming Soon', 'mallfinder'); ?></option>
                    <option value="closed" <?php selected($status, 'closed'); ?>><?php _e('Closed', 'mallfinder'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="store_floor"><?php _e('Floor', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="store_floor" id="store_floor" value="<?php echo esc_attr($floor); ?>" class="regular-text" placeholder="Ground Floor, 1st Floor, etc." />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="store_unit"><?php _e('Unit Number', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="store_unit" id="store_unit" value="<?php echo esc_attr($unit); ?>" class="regular-text" placeholder="G-12, 1F-45, etc." />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="store_phone"><?php _e('Phone', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="store_phone" id="store_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="store_website"><?php _e('Website', 'mallfinder'); ?></label></th>
            <td>
                <input type="url" name="store_website" id="store_website" value="<?php echo esc_attr($website); ?>" class="regular-text" placeholder="https://" />
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="store_opening_hours"><?php _e('Opening Hours', 'mallfinder'); ?></label></th>
            <td>
                <input type="text" name="store_opening_hours" id="store_opening_hours" value="<?php echo esc_attr($opening_hours); ?>" class="regular-text" placeholder="Mon-Sun: 10:00 AM - 10:00 PM" />
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Advertisement Details Meta Box Callback
 */
function mallfinder_ad_details_callback($post) {
    wp_nonce_field('mallfinder_ad_details', 'mallfinder_ad_details_nonce');
    
    $position = get_post_meta($post->ID, '_ad_position', true);
    $type = get_post_meta($post->ID, '_ad_type', true);
    $image_url = get_post_meta($post->ID, '_ad_image_url', true);
    $link_url = get_post_meta($post->ID, '_ad_link_url', true);
    $html_code = get_post_meta($post->ID, '_ad_html_code', true);
    $is_active = get_post_meta($post->ID, '_ad_is_active', true);
    $sort_order = get_post_meta($post->ID, '_ad_sort_order', true);
    $mall_id = get_post_meta($post->ID, '_ad_mall_id', true);
    
    // Get all malls for mall-specific ads
    $malls = get_posts(array(
        'post_type'      => 'mall',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="ad_position"><?php _e('Position', 'mallfinder'); ?></label></th>
            <td>
                <select name="ad_position" id="ad_position">
                    <option value="header" <?php selected($position, 'header'); ?>><?php _e('Header', 'mallfinder'); ?></option>
                    <option value="footer" <?php selected($position, 'footer'); ?>><?php _e('Footer', 'mallfinder'); ?></option>
                    <option value="sidebar" <?php selected($position, 'sidebar'); ?>><?php _e('Sidebar', 'mallfinder'); ?></option>
                    <option value="mall_header" <?php selected($position, 'mall_header'); ?>><?php _e('Mall Page Header', 'mallfinder'); ?></option>
                    <option value="mall_footer" <?php selected($position, 'mall_footer'); ?>><?php _e('Mall Page Footer', 'mallfinder'); ?></option>
                    <option value="mall_sidebar" <?php selected($position, 'mall_sidebar'); ?>><?php _e('Mall Page Sidebar', 'mallfinder'); ?></option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="ad_type"><?php _e('Type', 'mallfinder'); ?></label></th>
            <td>
                <select name="ad_type" id="ad_type">
                    <option value="image" <?php selected($type, 'image'); ?>><?php _e('Image', 'mallfinder'); ?></option>
                    <option value="html" <?php selected($type, 'html'); ?>><?php _e('HTML Code', 'mallfinder'); ?></option>
                </select>
            </td>
        </tr>
        <tr class="ad-image-field">
            <th scope="row"><label for="ad_image_url"><?php _e('Image URL', 'mallfinder'); ?></label></th>
            <td>
                <input type="url" name="ad_image_url" id="ad_image_url" value="<?php echo esc_url($image_url); ?>" class="large-text" />
                <button type="button" class="button upload-ad-image"><?php _e('Upload Image', 'mallfinder'); ?></button>
            </td>
        </tr>
        <tr class="ad-image-field">
            <th scope="row"><label for="ad_link_url"><?php _e('Link URL', 'mallfinder'); ?></label></th>
            <td>
                <input type="url" name="ad_link_url" id="ad_link_url" value="<?php echo esc_url($link_url); ?>" class="large-text" />
            </td>
        </tr>
        <tr class="ad-html-field" style="<?php echo $type !== 'html' ? 'display:none;' : ''; ?>">
            <th scope="row"><label for="ad_html_code"><?php _e('HTML Code', 'mallfinder'); ?></label></th>
            <td>
                <textarea name="ad_html_code" id="ad_html_code" rows="5" class="large-text code"><?php echo esc_textarea($html_code); ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="ad_mall_id"><?php _e('Specific Mall (Optional)', 'mallfinder'); ?></label></th>
            <td>
                <select name="ad_mall_id" id="ad_mall_id">
                    <option value=""><?php _e('Global (Show on all pages)', 'mallfinder'); ?></option>
                    <?php foreach ($malls as $mall) : ?>
                        <option value="<?php echo esc_attr($mall->ID); ?>" <?php selected($mall_id, $mall->ID); ?>>
                            <?php echo esc_html($mall->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description"><?php _e('Select a specific mall to show this ad only on that mall\'s page.', 'mallfinder'); ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="ad_sort_order"><?php _e('Sort Order', 'mallfinder'); ?></label></th>
            <td>
                <input type="number" name="ad_sort_order" id="ad_sort_order" value="<?php echo esc_attr($sort_order); ?>" class="small-text" min="0" />
            </td>
        </tr>
        <tr>
            <th scope="row"><?php _e('Active', 'mallfinder'); ?></th>
            <td>
                <label>
                    <input type="checkbox" name="ad_is_active" id="ad_is_active" value="1" <?php checked($is_active, '1'); ?> />
                    <?php _e('This ad is active', 'mallfinder'); ?>
                </label>
            </td>
        </tr>
    </table>
    
    <script>
    jQuery(document).ready(function($) {
        $('#ad_type').change(function() {
            if ($(this).val() === 'html') {
                $('.ad-image-field').hide();
                $('.ad-html-field').show();
            } else {
                $('.ad-image-field').show();
                $('.ad-html-field').hide();
            }
        });
        
        $('.upload-ad-image').click(function(e) {
            e.preventDefault();
            var mediaUploader = wp.media({
                title: '<?php _e('Select Advertisement Image', 'mallfinder'); ?>',
                button: { text: '<?php _e('Use this image', 'mallfinder'); ?>' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#ad_image_url').val(attachment.url);
            });
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

/**
 * Save Mall Meta
 */
function mallfinder_save_mall_meta($post_id) {
    // Check nonce
    if (!isset($_POST['mallfinder_mall_details_nonce']) || 
        !wp_verify_nonce($_POST['mallfinder_mall_details_nonce'], 'mallfinder_mall_details')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save fields
    $fields = array(
        'mall_status'        => '_mall_status',
        'mall_address'       => '_mall_address',
        'mall_phone'         => '_mall_phone',
        'mall_website'       => '_mall_website',
        'mall_opening_hours' => '_mall_opening_hours',
        'mall_expected_opening' => '_mall_expected_opening',
        'mall_stores_count'  => '_mall_stores_count',
        'mall_latitude'      => '_mall_latitude',
        'mall_longitude'     => '_mall_longitude',
        'mall_meta_title'    => '_mall_meta_title',
        'mall_meta_description' => '_mall_meta_description',
        'mall_keywords'      => '_mall_keywords',
    );
    
    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            if ($field === 'mall_stores_count') {
                update_post_meta($post_id, $meta_key, intval($_POST[$field]));
            } elseif ($field === 'mall_website') {
                update_post_meta($post_id, $meta_key, esc_url_raw($_POST[$field]));
            } else {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        }
    }
}
add_action('save_post_mall', 'mallfinder_save_mall_meta');

/**
 * Save Store Meta
 */
function mallfinder_save_store_meta($post_id) {
    // Check nonce
    if (!isset($_POST['mallfinder_store_details_nonce']) || 
        !wp_verify_nonce($_POST['mallfinder_store_details_nonce'], 'mallfinder_store_details')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save fields
    $fields = array(
        'store_mall_id'      => '_store_mall_id',
        'store_status'       => '_store_status',
        'store_floor'        => '_store_floor',
        'store_unit'         => '_store_unit',
        'store_phone'        => '_store_phone',
        'store_website'      => '_store_website',
        'store_opening_hours' => '_store_opening_hours',
    );
    
    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            if ($field === 'store_mall_id') {
                update_post_meta($post_id, $meta_key, intval($_POST[$field]));
            } elseif ($field === 'store_website') {
                update_post_meta($post_id, $meta_key, esc_url_raw($_POST[$field]));
            } else {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    // Also save as taxonomy for store category
    if (isset($_POST['tax_input']['store_category'])) {
        // WordPress handles this automatically
    }
}
add_action('save_post_store', 'mallfinder_save_store_meta');

/**
 * Save Advertisement Meta
 */
function mallfinder_save_ad_meta($post_id) {
    // Check nonce
    if (!isset($_POST['mallfinder_ad_details_nonce']) || 
        !wp_verify_nonce($_POST['mallfinder_ad_details_nonce'], 'mallfinder_ad_details')) {
        return;
    }
    
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save fields
    $fields = array(
        'ad_position'   => '_ad_position',
        'ad_type'       => '_ad_type',
        'ad_image_url'  => '_ad_image_url',
        'ad_link_url'   => '_ad_link_url',
        'ad_html_code'  => '_ad_html_code',
        'ad_mall_id'    => '_ad_mall_id',
        'ad_sort_order' => '_ad_sort_order',
    );
    
    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            if ($field === 'ad_mall_id' || $field === 'ad_sort_order') {
                update_post_meta($post_id, $meta_key, intval($_POST[$field]));
            } elseif ($field === 'ad_image_url' || $field === 'ad_link_url') {
                update_post_meta($post_id, $meta_key, esc_url_raw($_POST[$field]));
            } elseif ($field === 'ad_html_code') {
                update_post_meta($post_id, $meta_key, wp_kses_post($_POST[$field]));
            } else {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
            }
        }
    }
    
    // Active checkbox
    $is_active = isset($_POST['ad_is_active']) ? '1' : '0';
    update_post_meta($post_id, '_ad_is_active', $is_active);
}
add_action('save_post_advertisement', 'mallfinder_save_ad_meta');

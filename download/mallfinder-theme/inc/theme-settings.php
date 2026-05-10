<?php
/**
 * Theme Settings Page
 *
 * @package MallFinder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Theme Options Page
 */
function mallfinder_add_theme_options_page() {
    add_menu_page(
        __('MallFinder Settings', 'mallfinder'),
        __('MallFinder Settings', 'mallfinder'),
        'manage_options',
        'mallfinder-settings',
        'mallfinder_theme_settings_page',
        'dashicons-admin-settings',
        60
    );
}
add_action('admin_menu', 'mallfinder_add_theme_options_page');

/**
 * Register Settings
 */
function mallfinder_register_settings() {
    // General Settings
    register_setting('mallfinder_general', 'mallfinder_site_phone');
    register_setting('mallfinder_general', 'mallfinder_site_email');
    register_setting('mallfinder_general', 'mallfinder_site_address');
    
    // Map Settings
    register_setting('mallfinder_map', 'mallfinder_default_lat');
    register_setting('mallfinder_map', 'mallfinder_default_lng');
    register_setting('mallfinder_map', 'mallfinder_default_zoom');
    register_setting('mallfinder_map', 'mallfinder_map_api_key');
    
    // Social Media
    register_setting('mallfinder_social', 'mallfinder_facebook');
    register_setting('mallfinder_social', 'mallfinder_twitter');
    register_setting('mallfinder_social', 'mallfinder_instagram');
    register_setting('mallfinder_social', 'mallfinder_linkedin');
    register_setting('mallfinder_social', 'mallfinder_youtube');
    register_setting('mallfinder_social', 'mallfinder_whatsapp');
    
    // SEO Settings
    register_setting('mallfinder_seo', 'mallfinder_meta_title');
    register_setting('mallfinder_seo', 'mallfinder_meta_description');
    register_setting('mallfinder_seo', 'mallfinder_meta_keywords');
    
    // Add settings sections
    add_settings_section(
        'mallfinder_general_section',
        __('General Settings', 'mallfinder'),
        null,
        'mallfinder_general'
    );
    
    add_settings_section(
        'mallfinder_map_section',
        __('Map Settings', 'mallfinder'),
        null,
        'mallfinder_map'
    );
    
    add_settings_section(
        'mallfinder_social_section',
        __('Social Media Links', 'mallfinder'),
        null,
        'mallfinder_social'
    );
    
    add_settings_section(
        'mallfinder_seo_section',
        __('SEO Settings', 'mallfinder'),
        null,
        'mallfinder_seo'
    );
    
    // General Settings Fields
    add_settings_field(
        'mallfinder_site_phone',
        __('Phone Number', 'mallfinder'),
        'mallfinder_text_field_callback',
        'mallfinder_general',
        'mallfinder_general_section',
        array('field' => 'mallfinder_site_phone', 'description' => __('Main contact phone number', 'mallfinder'))
    );
    
    add_settings_field(
        'mallfinder_site_email',
        __('Email Address', 'mallfinder'),
        'mallfinder_email_field_callback',
        'mallfinder_general',
        'mallfinder_general_section',
        array('field' => 'mallfinder_site_email', 'description' => __('Main contact email address', 'mallfinder'))
    );
    
    add_settings_field(
        'mallfinder_site_address',
        __('Business Address', 'mallfinder'),
        'mallfinder_textarea_field_callback',
        'mallfinder_general',
        'mallfinder_general_section',
        array('field' => 'mallfinder_site_address', 'description' => __('Business address for footer', 'mallfinder'))
    );
    
    // Map Settings Fields
    add_settings_field(
        'mallfinder_default_lat',
        __('Default Latitude', 'mallfinder'),
        'mallfinder_text_field_callback',
        'mallfinder_map',
        'mallfinder_map_section',
        array('field' => 'mallfinder_default_lat', 'description' => __('Default map center latitude', 'mallfinder'))
    );
    
    add_settings_field(
        'mallfinder_default_lng',
        __('Default Longitude', 'mallfinder'),
        'mallfinder_text_field_callback',
        'mallfinder_map',
        'mallfinder_map_section',
        array('field' => 'mallfinder_default_lng', 'description' => __('Default map center longitude', 'mallfinder'))
    );
    
    add_settings_field(
        'mallfinder_default_zoom',
        __('Default Zoom Level', 'mallfinder'),
        'mallfinder_number_field_callback',
        'mallfinder_map',
        'mallfinder_map_section',
        array('field' => 'mallfinder_default_zoom', 'description' => __('Default zoom level (1-18)', 'mallfinder'), 'min' => 1, 'max' => 18)
    );
    
    add_settings_field(
        'mallfinder_map_api_key',
        __('Map API Key (Optional)', 'mallfinder'),
        'mallfinder_text_field_callback',
        'mallfinder_map',
        'mallfinder_map_section',
        array('field' => 'mallfinder_map_api_key', 'description' => __('Optional: Google Maps or other map provider API key', 'mallfinder'))
    );
    
    // Social Media Fields
    $social_platforms = array(
        'facebook'  => array('label' => 'Facebook URL', 'icon' => 'facebook'),
        'twitter'   => array('label' => 'Twitter/X URL', 'icon' => 'twitter'),
        'instagram' => array('label' => 'Instagram URL', 'icon' => 'instagram'),
        'linkedin'  => array('label' => 'LinkedIn URL', 'icon' => 'linkedin'),
        'youtube'   => array('label' => 'YouTube URL', 'icon' => 'youtube'),
        'whatsapp'  => array('label' => 'WhatsApp Number', 'icon' => 'whatsapp'),
    );
    
    foreach ($social_platforms as $platform => $data) {
        add_settings_field(
            'mallfinder_' . $platform,
            $data['label'],
            'mallfinder_text_field_callback',
            'mallfinder_social',
            'mallfinder_social_section',
            array('field' => 'mallfinder_' . $platform)
        );
    }
    
    // SEO Settings Fields
    add_settings_field(
        'mallfinder_meta_title',
        __('Site Meta Title', 'mallfinder'),
        'mallfinder_text_field_callback',
        'mallfinder_seo',
        'mallfinder_seo_section',
        array('field' => 'mallfinder_meta_title', 'description' => __('Default meta title for SEO', 'mallfinder'))
    );
    
    add_settings_field(
        'mallfinder_meta_description',
        __('Site Meta Description', 'mallfinder'),
        'mallfinder_textarea_field_callback',
        'mallfinder_seo',
        'mallfinder_seo_section',
        array('field' => 'mallfinder_meta_description', 'description' => __('Default meta description for SEO', 'mallfinder'))
    );
    
    add_settings_field(
        'mallfinder_meta_keywords',
        __('Meta Keywords', 'mallfinder'),
        'mallfinder_text_field_callback',
        'mallfinder_seo',
        'mallfinder_seo_section',
        array('field' => 'mallfinder_meta_keywords', 'description' => __('Comma-separated keywords', 'mallfinder'))
    );
}
add_action('admin_init', 'mallfinder_register_settings');

/**
 * Field Callbacks
 */
function mallfinder_text_field_callback($args) {
    $value = get_option($args['field'], '');
    echo '<input type="text" name="' . esc_attr($args['field']) . '" value="' . esc_attr($value) . '" class="regular-text">';
    if (!empty($args['description'])) {
        echo '<p class="description">' . esc_html($args['description']) . '</p>';
    }
}

function mallfinder_email_field_callback($args) {
    $value = get_option($args['field'], '');
    echo '<input type="email" name="' . esc_attr($args['field']) . '" value="' . esc_attr($value) . '" class="regular-text">';
    if (!empty($args['description'])) {
        echo '<p class="description">' . esc_html($args['description']) . '</p>';
    }
}

function mallfinder_textarea_field_callback($args) {
    $value = get_option($args['field'], '');
    echo '<textarea name="' . esc_attr($args['field']) . '" rows="3" class="large-text">' . esc_textarea($value) . '</textarea>';
    if (!empty($args['description'])) {
        echo '<p class="description">' . esc_html($args['description']) . '</p>';
    }
}

function mallfinder_number_field_callback($args) {
    $value = get_option($args['field'], '');
    $min = isset($args['min']) ? 'min="' . $args['min'] . '"' : '';
    $max = isset($args['max']) ? 'max="' . $args['max'] . '"' : '';
    echo '<input type="number" name="' . esc_attr($args['field']) . '" value="' . esc_attr($value) . '" class="small-text" ' . $min . ' ' . $max . '>';
    if (!empty($args['description'])) {
        echo '<p class="description">' . esc_html($args['description']) . '</p>';
    }
}

/**
 * Theme Settings Page HTML
 */
function mallfinder_theme_settings_page() {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
    ?>
    <div class="wrap">
        <h1><?php _e('MallFinder Theme Settings', 'mallfinder'); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="?page=mallfinder-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">
                <?php _e('General', 'mallfinder'); ?>
            </a>
            <a href="?page=mallfinder-settings&tab=map" class="nav-tab <?php echo $active_tab == 'map' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Map Settings', 'mallfinder'); ?>
            </a>
            <a href="?page=mallfinder-settings&tab=social" class="nav-tab <?php echo $active_tab == 'social' ? 'nav-tab-active' : ''; ?>">
                <?php _e('Social Media', 'mallfinder'); ?>
            </a>
            <a href="?page=mallfinder-settings&tab=seo" class="nav-tab <?php echo $active_tab == 'seo' ? 'nav-tab-active' : ''; ?>">
                <?php _e('SEO', 'mallfinder'); ?>
            </a>
        </h2>
        
        <form method="post" action="options.php">
            <?php
            switch ($active_tab) {
                case 'general':
                    settings_fields('mallfinder_general');
                    do_settings_sections('mallfinder_general');
                    break;
                case 'map':
                    settings_fields('mallfinder_map');
                    do_settings_sections('mallfinder_map');
                    break;
                case 'social':
                    settings_fields('mallfinder_social');
                    do_settings_sections('mallfinder_social');
                    break;
                case 'seo':
                    settings_fields('mallfinder_seo');
                    do_settings_sections('mallfinder_seo');
                    break;
            }
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Add Custom Meta Tags
 */
function mallfinder_add_meta_tags() {
    $meta_title = get_option('mallfinder_meta_title', get_bloginfo('name'));
    $meta_description = get_option('mallfinder_meta_description', get_bloginfo('description'));
    $meta_keywords = get_option('mallfinder_meta_keywords', '');
    
    echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
    
    if ($meta_keywords) {
        echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">' . "\n";
    }
    
    // Open Graph tags
    echo '<meta property="og:title" content="' . esc_attr($meta_title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($meta_description) . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:url" content="' . esc_url(home_url()) . '">' . "\n";
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($meta_title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '">' . "\n";
}
add_action('wp_head', 'mallfinder_add_meta_tags');

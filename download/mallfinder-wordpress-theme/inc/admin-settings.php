<?php
/**
 * Admin Settings Page
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Admin Menu
 */
function mallfinder_add_admin_menu() {
    add_menu_page(
        __('MallFinder Settings', 'mallfinder'),
        __('MallFinder', 'mallfinder'),
        'manage_options',
        'mallfinder-settings',
        'mallfinder_settings_page',
        'dashicons-building',
        30
    );
    
    add_submenu_page(
        'mallfinder-settings',
        __('Site Settings', 'mallfinder'),
        __('Site Settings', 'mallfinder'),
        'manage_options',
        'mallfinder-settings',
        'mallfinder_settings_page'
    );
    
    add_submenu_page(
        'mallfinder-settings',
        __('Social Media', 'mallfinder'),
        __('Social Media', 'mallfinder'),
        'manage_options',
        'mallfinder-social',
        'mallfinder_social_page'
    );
}
add_action('admin_menu', 'mallfinder_add_admin_menu');

/**
 * Register Settings
 */
function mallfinder_register_settings() {
    register_setting('mallfinder_settings', 'mallfinder_site_name');
    register_setting('mallfinder_settings', 'mallfinder_site_tagline');
    register_setting('mallfinder_settings', 'mallfinder_logo');
    register_setting('mallfinder_settings', 'mallfinder_default_lat');
    register_setting('mallfinder_settings', 'mallfinder_default_lng');
    register_setting('mallfinder_settings', 'mallfinder_default_zoom');
    register_setting('mallfinder_settings', 'mallfinder_contact_email');
    register_setting('mallfinder_settings', 'mallfinder_contact_phone');
    register_setting('mallfinder_settings', 'mallfinder_contact_address');
    
    // Social Media Settings
    register_setting('mallfinder_social', 'mallfinder_facebook_url');
    register_setting('mallfinder_social', 'mallfinder_twitter_url');
    register_setting('mallfinder_social', 'mallfinder_instagram_url');
    register_setting('mallfinder_social', 'mallfinder_linkedin_url');
    register_setting('mallfinder_social', 'mallfinder_youtube_url');
    register_setting('mallfinder_social', 'mallfinder_whatsapp_number');
}
add_action('admin_init', 'mallfinder_register_settings');

/**
 * Settings Page
 */
function mallfinder_settings_page() {
    ?>
    <div class="wrap admin-page">
        <h1><?php _e('MallFinder Site Settings', 'mallfinder'); ?></h1>
        
        <form method="post" action="options.php">
            <?php settings_fields('mallfinder_settings'); ?>
            
            <div class="admin-card">
                <h2><?php _e('Site Identity', 'mallfinder'); ?></h2>
                
                <div class="admin-field">
                    <label for="mallfinder_site_name"><?php _e('Site Name', 'mallfinder'); ?></label>
                    <input type="text" id="mallfinder_site_name" name="mallfinder_site_name" 
                           value="<?php echo esc_attr(get_option('mallfinder_site_name', 'MallFinder')); ?>" 
                           class="regular-text" />
                    <p class="description"><?php _e('This name appears in the header and browser tab.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_site_tagline"><?php _e('Site Tagline', 'mallfinder'); ?></label>
                    <input type="text" id="mallfinder_site_tagline" name="mallfinder_site_tagline" 
                           value="<?php echo esc_attr(get_option('mallfinder_site_tagline', 'Discover Shopping Destinations')); ?>" 
                           class="regular-text" />
                    <p class="description"><?php _e('A short description that appears below the site name.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_logo"><?php _e('Site Logo URL', 'mallfinder'); ?></label>
                    <input type="text" id="mallfinder_logo" name="mallfinder_logo" 
                           value="<?php echo esc_attr(get_option('mallfinder_logo')); ?>" 
                           class="regular-text" />
                    <button type="button" class="button upload-logo-button"><?php _e('Upload Logo', 'mallfinder'); ?></button>
                    <p class="description"><?php _e('Upload a logo or enter a URL. Recommended size: 200x50px.', 'mallfinder'); ?></p>
                    <?php $logo = get_option('mallfinder_logo'); ?>
                    <?php if ($logo) : ?>
                        <div class="logo-preview" style="margin-top: 10px;">
                            <img src="<?php echo esc_url($logo); ?>" alt="Logo" style="max-width: 200px; max-height: 50px;" />
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="admin-card">
                <h2><?php _e('Map Settings', 'mallfinder'); ?></h2>
                
                <div class="admin-field">
                    <label for="mallfinder_default_lat"><?php _e('Default Latitude', 'mallfinder'); ?></label>
                    <input type="text" id="mallfinder_default_lat" name="mallfinder_default_lat" 
                           value="<?php echo esc_attr(get_option('mallfinder_default_lat', '20.5937')); ?>" 
                           class="regular-text" />
                    <p class="description"><?php _e('Default center latitude for the map.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_default_lng"><?php _e('Default Longitude', 'mallfinder'); ?></label>
                    <input type="text" id="mallfinder_default_lng" name="mallfinder_default_lng" 
                           value="<?php echo esc_attr(get_option('mallfinder_default_lng', '78.9629')); ?>" 
                           class="regular-text" />
                    <p class="description"><?php _e('Default center longitude for the map.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_default_zoom"><?php _e('Default Zoom Level', 'mallfinder'); ?></label>
                    <input type="number" id="mallfinder_default_zoom" name="mallfinder_default_zoom" 
                           value="<?php echo esc_attr(get_option('mallfinder_default_zoom', '5')); ?>" 
                           min="1" max="18" class="small-text" />
                    <p class="description"><?php _e('Default zoom level for the map (1-18).', 'mallfinder'); ?></p>
                </div>
            </div>
            
            <div class="admin-card">
                <h2><?php _e('Contact Information', 'mallfinder'); ?></h2>
                
                <div class="admin-field">
                    <label for="mallfinder_contact_email"><?php _e('Contact Email', 'mallfinder'); ?></label>
                    <input type="email" id="mallfinder_contact_email" name="mallfinder_contact_email" 
                           value="<?php echo esc_attr(get_option('mallfinder_contact_email')); ?>" 
                           class="regular-text" />
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_contact_phone"><?php _e('Contact Phone', 'mallfinder'); ?></label>
                    <input type="tel" id="mallfinder_contact_phone" name="mallfinder_contact_phone" 
                           value="<?php echo esc_attr(get_option('mallfinder_contact_phone')); ?>" 
                           class="regular-text" />
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_contact_address"><?php _e('Contact Address', 'mallfinder'); ?></label>
                    <textarea id="mallfinder_contact_address" name="mallfinder_contact_address" 
                              class="regular-text" rows="3"><?php echo esc_textarea(get_option('mallfinder_contact_address')); ?></textarea>
                </div>
            </div>
            
            <?php submit_button(__('Save Settings', 'mallfinder')); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.upload-logo-button').click(function(e) {
            e.preventDefault();
            var mediaUploader = wp.media({
                title: '<?php _e('Select Logo', 'mallfinder'); ?>',
                button: { text: '<?php _e('Use this image', 'mallfinder'); ?>' },
                multiple: false
            });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#mallfinder_logo').val(attachment.url);
                $('.logo-preview').html('<img src="' + attachment.url + '" alt="Logo" style="max-width: 200px; max-height: 50px;" />');
            });
            mediaUploader.open();
        });
    });
    </script>
    <?php
}

/**
 * Social Media Page
 */
function mallfinder_social_page() {
    ?>
    <div class="wrap admin-page">
        <h1><?php _e('Social Media Settings', 'mallfinder'); ?></h1>
        
        <form method="post" action="options.php">
            <?php settings_fields('mallfinder_social'); ?>
            
            <div class="admin-card">
                <h2><?php _e('Social Media Accounts', 'mallfinder'); ?></h2>
                
                <div class="admin-field">
                    <label for="mallfinder_facebook_url">
                        <span class="dashicons dashicons-facebook" style="color: #1877f2;"></span>
                        <?php _e('Facebook URL', 'mallfinder'); ?>
                    </label>
                    <input type="url" id="mallfinder_facebook_url" name="mallfinder_facebook_url" 
                           value="<?php echo esc_attr(get_option('mallfinder_facebook_url')); ?>" 
                           class="regular-text" placeholder="https://facebook.com/yourpage" />
                    <p class="description"><?php _e('Enter your Facebook page URL.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_twitter_url">
                        <span class="dashicons dashicons-twitter" style="color: #1da1f2;"></span>
                        <?php _e('X (Twitter) URL', 'mallfinder'); ?>
                    </label>
                    <input type="url" id="mallfinder_twitter_url" name="mallfinder_twitter_url" 
                           value="<?php echo esc_attr(get_option('mallfinder_twitter_url')); ?>" 
                           class="regular-text" placeholder="https://x.com/yourhandle" />
                    <p class="description"><?php _e('Enter your X (Twitter) profile URL.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_instagram_url">
                        <span class="dashicons dashicons-instagram" style="color: #e4405f;"></span>
                        <?php _e('Instagram URL', 'mallfinder'); ?>
                    </label>
                    <input type="url" id="mallfinder_instagram_url" name="mallfinder_instagram_url" 
                           value="<?php echo esc_attr(get_option('mallfinder_instagram_url')); ?>" 
                           class="regular-text" placeholder="https://instagram.com/yourhandle" />
                    <p class="description"><?php _e('Enter your Instagram profile URL.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_linkedin_url">
                        <span class="dashicons dashicons-linkedin" style="color: #0a66c2;"></span>
                        <?php _e('LinkedIn URL', 'mallfinder'); ?>
                    </label>
                    <input type="url" id="mallfinder_linkedin_url" name="mallfinder_linkedin_url" 
                           value="<?php echo esc_attr(get_option('mallfinder_linkedin_url')); ?>" 
                           class="regular-text" placeholder="https://linkedin.com/company/yourcompany" />
                    <p class="description"><?php _e('Enter your LinkedIn company page URL.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_youtube_url">
                        <span class="dashicons dashicons-youtube" style="color: #ff0000;"></span>
                        <?php _e('YouTube URL', 'mallfinder'); ?>
                    </label>
                    <input type="url" id="mallfinder_youtube_url" name="mallfinder_youtube_url" 
                           value="<?php echo esc_attr(get_option('mallfinder_youtube_url')); ?>" 
                           class="regular-text" placeholder="https://youtube.com/@yourchannel" />
                    <p class="description"><?php _e('Enter your YouTube channel URL.', 'mallfinder'); ?></p>
                </div>
                
                <div class="admin-field">
                    <label for="mallfinder_whatsapp_number">
                        <span class="dashicons dashicons-format-chat" style="color: #25d366;"></span>
                        <?php _e('WhatsApp Number', 'mallfinder'); ?>
                    </label>
                    <input type="tel" id="mallfinder_whatsapp_number" name="mallfinder_whatsapp_number" 
                           value="<?php echo esc_attr(get_option('mallfinder_whatsapp_number')); ?>" 
                           class="regular-text" placeholder="+1234567890" />
                    <p class="description"><?php _e('Enter WhatsApp number with country code.', 'mallfinder'); ?></p>
                </div>
            </div>
            
            <?php submit_button(__('Save Social Media Settings', 'mallfinder')); ?>
        </form>
    </div>
    <?php
}

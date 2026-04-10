<?php
/**
 * Social Media Functions
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Social Media Links
 */
function mallfinder_get_social_links() {
    return array(
        'facebook'  => get_option('mallfinder_facebook_url'),
        'twitter'   => get_option('mallfinder_twitter_url'),
        'instagram' => get_option('mallfinder_instagram_url'),
        'linkedin'  => get_option('mallfinder_linkedin_url'),
        'youtube'   => get_option('mallfinder_youtube_url'),
        'whatsapp'  => get_option('mallfinder_whatsapp_number'),
    );
}

/**
 * Check if any social media is configured
 */
function mallfinder_has_social_media() {
    $links = mallfinder_get_social_links();
    foreach ($links as $link) {
        if (!empty($link)) {
            return true;
        }
    }
    return false;
}

/**
 * Display Social Media Icons (Header Style)
 */
function mallfinder_social_icons_header() {
    $links = mallfinder_get_social_links();
    
    if (!mallfinder_has_social_media()) {
        return;
    }
    ?>
    <div class="social-icons">
        <?php if ($links['facebook']) : ?>
            <a href="<?php echo esc_url($links['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="facebook" title="<?php esc_attr_e('Follow us on Facebook', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['twitter']) : ?>
            <a href="<?php echo esc_url($links['twitter']); ?>" target="_blank" rel="noopener noreferrer" class="twitter" title="<?php esc_attr_e('Follow us on X', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['instagram']) : ?>
            <a href="<?php echo esc_url($links['instagram']); ?>" target="_blank" rel="noopener noreferrer" class="instagram" title="<?php esc_attr_e('Follow us on Instagram', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['linkedin']) : ?>
            <a href="<?php echo esc_url($links['linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="linkedin" title="<?php esc_attr_e('Connect on LinkedIn', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['youtube']) : ?>
            <a href="<?php echo esc_url($links['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="youtube" title="<?php esc_attr_e('Subscribe on YouTube', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['whatsapp']) : ?>
            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $links['whatsapp'])); ?>" target="_blank" rel="noopener noreferrer" class="whatsapp" title="<?php esc_attr_e('Chat on WhatsApp', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Display Social Media Icons (Footer Style)
 */
function mallfinder_social_icons_footer() {
    $links = mallfinder_get_social_links();
    
    if (!mallfinder_has_social_media()) {
        return;
    }
    ?>
    <div class="footer-social">
        <?php if ($links['facebook']) : ?>
            <a href="<?php echo esc_url($links['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="facebook" title="<?php esc_attr_e('Follow us on Facebook', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['twitter']) : ?>
            <a href="<?php echo esc_url($links['twitter']); ?>" target="_blank" rel="noopener noreferrer" class="twitter" title="<?php esc_attr_e('Follow us on X', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['instagram']) : ?>
            <a href="<?php echo esc_url($links['instagram']); ?>" target="_blank" rel="noopener noreferrer" class="instagram" title="<?php esc_attr_e('Follow us on Instagram', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['linkedin']) : ?>
            <a href="<?php echo esc_url($links['linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="linkedin" title="<?php esc_attr_e('Connect on LinkedIn', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['youtube']) : ?>
            <a href="<?php echo esc_url($links['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="youtube" title="<?php esc_attr_e('Subscribe on YouTube', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
            </a>
        <?php endif; ?>
        
        <?php if ($links['whatsapp']) : ?>
            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $links['whatsapp'])); ?>" target="_blank" rel="noopener noreferrer" class="whatsapp" title="<?php esc_attr_e('Chat on WhatsApp', 'mallfinder'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </a>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Display Social Share Buttons
 */
function mallfinder_social_share($args = array()) {
    $defaults = array(
        'url'   => get_permalink(),
        'title' => get_the_title(),
    );
    $args = wp_parse_args($args, $defaults);
    
    $encoded_url = rawurlencode($args['url']);
    $encoded_title = rawurlencode($args['title']);
    ?>
    <div class="social-share">
        <span><?php _e('Share:', 'mallfinder'); ?></span>
        
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>" target="_blank" rel="noopener noreferrer" class="facebook" title="<?php esc_attr_e('Share on Facebook', 'mallfinder'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
        
        <a href="https://twitter.com/intent/tweet?url=<?php echo $encoded_url; ?>&text=<?php echo $encoded_title; ?>" target="_blank" rel="noopener noreferrer" class="twitter" title="<?php esc_attr_e('Share on X', 'mallfinder'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
        </a>
        
        <a href="https://wa.me/?text=<?php echo $encoded_title; ?>%20<?php echo $encoded_url; ?>" target="_blank" rel="noopener noreferrer" class="whatsapp" title="<?php esc_attr_e('Share on WhatsApp', 'mallfinder'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        
        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $encoded_url; ?>" target="_blank" rel="noopener noreferrer" class="linkedin" title="<?php esc_attr_e('Share on LinkedIn', 'mallfinder'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
        </a>
    </div>
    <?php
}

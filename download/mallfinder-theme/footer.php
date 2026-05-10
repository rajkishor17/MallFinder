<?php
/**
 * Footer Template
 *
 * @package MallFinder
 */
?>
</main>

<footer class="site-footer">
    <div class="footer-main">
        <div class="footer-grid">
            <!-- Brand Section -->
            <div class="footer-widget">
                <div class="footer-brand">
                    <?php if (has_custom_logo()) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <div class="logo-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 21h18"></path>
                                <path d="M5 21V7l8-4v18"></path>
                                <path d="M19 21V11l-6-4"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <span class="footer-brand-text"><?php bloginfo('name'); ?></span>
                </div>
                <p class="footer-description"><?php echo esc_html(get_bloginfo('description')); ?></p>
                
                <!-- Social Icons -->
                <div class="footer-social">
                    <?php $social = mallfinder_get_social_links(); ?>
                    <?php if (!empty($social['facebook'])) : ?>
                        <a href="<?php echo esc_url($social['facebook']); ?>" target="_blank" rel="noopener noreferrer" class="facebook" title="<?php esc_attr_e('Facebook', 'mallfinder'); ?>">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social['twitter'])) : ?>
                        <a href="<?php echo esc_url($social['twitter']); ?>" target="_blank" rel="noopener noreferrer" class="twitter" title="<?php esc_attr_e('X (Twitter)', 'mallfinder'); ?>">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social['instagram'])) : ?>
                        <a href="<?php echo esc_url($social['instagram']); ?>" target="_blank" rel="noopener noreferrer" class="instagram" title="<?php esc_attr_e('Instagram', 'mallfinder'); ?>">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social['linkedin'])) : ?>
                        <a href="<?php echo esc_url($social['linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="linkedin" title="<?php esc_attr_e('LinkedIn', 'mallfinder'); ?>">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social['youtube'])) : ?>
                        <a href="<?php echo esc_url($social['youtube']); ?>" target="_blank" rel="noopener noreferrer" class="youtube" title="<?php esc_attr_e('YouTube', 'mallfinder'); ?>">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($social['whatsapp'])) : ?>
                        <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $social['whatsapp'])); ?>" target="_blank" rel="noopener noreferrer" class="whatsapp" title="<?php esc_attr_e('WhatsApp', 'mallfinder'); ?>">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-widget">
                <h4><?php _e('Quick Links', 'mallfinder'); ?></h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'mallfinder'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About Us', 'mallfinder'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact Us', 'mallfinder'); ?></a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="footer-widget">
                <h4><?php _e('Categories', 'mallfinder'); ?></h4>
                <ul>
                    <li><a href="<?php echo get_post_type_archive_link('mall'); ?>?category=existing"><?php _e('Existing Malls', 'mallfinder'); ?></a></li>
                    <li><a href="<?php echo get_post_type_archive_link('mall'); ?>?category=upcoming"><?php _e('Upcoming Malls', 'mallfinder'); ?></a></li>
                    <li><a href="<?php echo get_post_type_archive_link('store'); ?>"><?php _e('All Stores', 'mallfinder'); ?></a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="footer-widget">
                <h4><?php _e('Contact', 'mallfinder'); ?></h4>
                <ul>
                    <?php if (!empty($social['whatsapp'])) : ?>
                        <li>
                            <a href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $social['whatsapp'])); ?>" target="_blank" rel="noopener noreferrer">
                                <?php _e('Chat with us', 'mallfinder'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Send us a message →', 'mallfinder'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="footer-bottom">
        <div class="footer-bottom-content">
            <p class="footer-copyright">© <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'mallfinder'); ?></p>
            <div class="footer-links">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About', 'mallfinder'); ?></a>
                <a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact', 'mallfinder'); ?></a>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>

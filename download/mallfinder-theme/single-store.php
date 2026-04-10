<?php
/**
 * Single Store Template
 *
 * @package MallFinder
 */

get_header();

while (have_posts()) : the_post();
    $store_id = get_the_ID();
    $mall_id = get_post_meta($store_id, '_store_mall', true);
    $logo = mallfinder_get_store_logo($store_id);
    $floor = get_post_meta($store_id, '_store_floor', true);
    $unit = get_post_meta($store_id, '_store_unit', true);
    $phone = get_post_meta($store_id, '_store_phone', true);
    $email = get_post_meta($store_id, '_store_email', true);
    $website = get_post_meta($store_id, '_store_website', true);
    $opening_hours = get_post_meta($store_id, '_store_opening_hours', true);
?>

<div class="page-header">
    <div class="container">
        <?php mallfinder_breadcrumbs(); ?>
        <h1><?php the_title(); ?></h1>
        <?php if ($mall_id) : ?>
            <p><?php _e('Located at', 'mallfinder'); ?>: <a href="<?php echo get_permalink($mall_id); ?>"><?php echo get_the_title($mall_id); ?></a></p>
        <?php endif; ?>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="mall-content">
            <div class="mall-main">
                <!-- Store Logo -->
                <?php if ($logo) : ?>
                    <div style="margin-bottom: 2rem;">
                        <img src="<?php echo esc_url($logo); ?>" alt="<?php the_title_attribute(); ?>" style="max-width: 200px; border-radius: var(--radius-md);">
                    </div>
                <?php endif; ?>
                
                <!-- Description -->
                <h2><?php _e('About', 'mallfinder'); ?></h2>
                <div class="mall-description">
                    <?php the_content(); ?>
                </div>
                
                <!-- Store Info -->
                <div class="info-grid" style="margin-top: 2rem;">
                    <?php if ($floor) : ?>
                        <div class="info-item">
                            <div class="icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line></svg>
                            </div>
                            <div>
                                <span class="label"><?php _e('Floor', 'mallfinder'); ?></span>
                                <span class="value"><?php echo esc_html($floor); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($unit) : ?>
                        <div class="info-item">
                            <div class="icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><path d="M9 9h6v6H9z"></path></svg>
                            </div>
                            <div>
                                <span class="label"><?php _e('Unit/Shop', 'mallfinder'); ?></span>
                                <span class="value"><?php echo esc_html($unit); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($phone) : ?>
                        <div class="info-item">
                            <div class="icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </div>
                            <div>
                                <span class="label"><?php _e('Phone', 'mallfinder'); ?></span>
                                <span class="value"><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($email) : ?>
                        <div class="info-item">
                            <div class="icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </div>
                            <div>
                                <span class="label"><?php _e('Email', 'mallfinder'); ?></span>
                                <span class="value"><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($website) : ?>
                        <div class="info-item">
                            <div class="icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            </div>
                            <div>
                                <span class="label"><?php _e('Website', 'mallfinder'); ?></span>
                                <span class="value"><a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener">Visit Website</a></span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Opening Hours -->
                <?php if ($opening_hours) : ?>
                    <div style="margin-top: 2rem;">
                        <h3><?php _e('Operating Hours', 'mallfinder'); ?></h3>
                        <div style="background: var(--background-color); padding: 1rem; border-radius: var(--radius-md); white-space: pre-wrap;"><?php echo esc_html($opening_hours); ?></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="mall-sidebar">
                <!-- Mall Info -->
                <?php if ($mall_id) : ?>
                    <div class="sidebar-widget">
                        <h3><?php _e('Located At', 'mallfinder'); ?></h3>
                        <p><a href="<?php echo get_permalink($mall_id); ?>"><?php echo get_the_title($mall_id); ?></a></p>
                        <p style="color: var(--text-secondary); font-size: 0.875rem;"><?php echo esc_html(mallfinder_format_address($mall_id)); ?></p>
                        <a href="<?php echo get_permalink($mall_id); ?>" class="btn btn-outline btn-sm" style="margin-top: 0.5rem; display: inline-block;"><?php _e('View Mall', 'mallfinder'); ?></a>
                    </div>
                <?php endif; ?>
                
                <!-- Categories -->
                <?php
                $terms = get_the_terms($store_id, 'store_category');
                if ($terms && !is_wp_error($terms)) :
                ?>
                    <div class="sidebar-widget">
                        <h3><?php _e('Categories', 'mallfinder'); ?></h3>
                        <ul>
                            <?php foreach ($terms as $term) : ?>
                                <li><a href="<?php echo get_term_link($term); ?>"><?php echo esc_html($term->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
endwhile;

get_footer();

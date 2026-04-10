<?php
/**
 * Store Card Template Part
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

$store_id = get_the_ID();
$status = mallfinder_get_store_status($store_id);
$floor = mallfinder_get_store_floor($store_id);
$unit = mallfinder_get_store_unit($store_id);
$phone = mallfinder_get_store_phone($store_id);
$website = mallfinder_get_store_website($store_id);
$category = mallfinder_get_store_category($store_id);
?>

<article class="card" data-store-id="<?php echo esc_attr($store_id); ?>" data-status="<?php echo esc_attr($status); ?>">
    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="card-image">
            <?php the_post_thumbnail('medium', array('style' => 'object-fit: cover; height: 150px;')); ?>
        </a>
    <?php endif; ?>
    
    <div class="card-content">
        <?php if ($category) : ?>
            <span class="card-badge" style="background: var(--color-slate-100); color: var(--color-slate-600);">
                <?php echo esc_html($category->name); ?>
            </span>
        <?php endif; ?>
        
        <h4 style="margin: 0.5rem 0; font-size: 1rem;">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h4>
        
        <div style="font-size: 0.875rem; color: var(--color-slate-500);">
            <?php if ($floor || $unit) : ?>
                <p style="margin: 0.25rem 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;"><path d="M3 3h18v18H3zM9 3v18"/></svg>
                    <?php 
                    $location_parts = array();
                    if ($floor) $location_parts[] = sprintf(__('Floor %s', 'mallfinder'), esc_html($floor));
                    if ($unit) $location_parts[] = sprintf(__('Unit %s', 'mallfinder'), esc_html($unit));
                    echo implode(', ', $location_parts);
                    ?>
                </p>
            <?php endif; ?>
            
            <?php if ($phone) : ?>
                <p style="margin: 0.25rem 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.11 4.22 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+\-]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
                </p>
            <?php endif; ?>
            
            <?php if ($website) : ?>
                <p style="margin: 0.25rem 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle;"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Website', 'mallfinder'); ?></a>
                </p>
            <?php endif; ?>
        </div>
        
        <p style="margin: 0.5rem 0; font-size: 0.875rem; color: var(--color-slate-500);">
            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?>
        </p>
        
        <span style="display: inline-block; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;
            <?php
            switch ($status) {
                case 'open':
                    echo 'background: var(--color-emerald-100); color: var(--color-emerald-700);';
                    break;
                case 'coming_soon':
                    echo 'background: #fef3c7; color: #b45309;';
                    break;
                case 'closed':
                    echo 'background: #fee2e2; color: #b91c1c;';
                    break;
                default:
                    echo 'background: var(--color-slate-100); color: var(--color-slate-600);';
            }
            ?>
        ">
            <?php
            $status_labels = array(
                'open'        => __('Open', 'mallfinder'),
                'coming_soon' => __('Coming Soon', 'mallfinder'),
                'closed'      => __('Closed', 'mallfinder'),
            );
            echo esc_html($status_labels[$status] ?? 'Open');
            ?>
        </span>
    </div>
</article>

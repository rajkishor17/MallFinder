<?php
/**
 * Mall Card Template Part
 * 
 * @package MallFinder
 */

if (!defined('ABSPATH')) {
    exit;
}

$mall_id = get_the_ID();
$status = mallfinder_get_mall_status($mall_id);
$address = mallfinder_get_mall_address($mall_id);
$opening_hours = mallfinder_get_mall_opening_hours($mall_id);
$location = mallfinder_get_mall_location($mall_id);
?>

<article class="card" data-mall-id="<?php echo esc_attr($mall_id); ?>" data-status="<?php echo esc_attr($status); ?>">
    <a href="<?php the_permalink(); ?>" class="card-image">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium_large', array('style' => 'object-fit: cover;')); ?>
        <?php else : ?>
            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity: 0.5;"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/></svg>
            </div>
        <?php endif; ?>
    </a>
    
    <div class="card-content">
        <span class="card-badge <?php echo esc_attr($status); ?>">
            <?php echo esc_html($status === 'upcoming' ? __('Upcoming', 'mallfinder') : __('Existing', 'mallfinder')); ?>
        </span>
        
        <h3 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        
        <p class="card-meta">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.25rem;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?php echo esc_html(implode(', ', array_filter(array($location['area'], $location['city'], $location['state'])))); ?>
        </p>
        
        <?php if ($status === 'upcoming') : ?>
            <?php $expected = mallfinder_get_mall_expected_opening($mall_id); ?>
            <?php if ($expected) : ?>
                <p class="card-meta" style="color: var(--color-amber-600);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.25rem;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?php _e('Expected:', 'mallfinder'); ?> <?php echo esc_html(mallfinder_format_date($expected, 'M Y')); ?>
                </p>
            <?php endif; ?>
        <?php elseif ($opening_hours) : ?>
            <p class="card-meta">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.25rem;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?php echo esc_html($opening_hours); ?>
            </p>
        <?php endif; ?>
        
        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
            <a href="<?php the_permalink(); ?>" class="btn <?php echo $status === 'upcoming' ? 'btn-amber' : 'btn-primary'; ?>">
                <?php _e('View Details', 'mallfinder'); ?>
            </a>
        </div>
    </div>
</article>

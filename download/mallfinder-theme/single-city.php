<?php
/**
 * Single City Template
 *
 * @package MallFinder
 */

get_header();

while (have_posts()) : the_post();
    $city_id = get_the_ID();
    $state_id = get_post_meta($city_id, '_city_state', true);
?>

<div class="page-header">
    <div class="container">
        <?php mallfinder_breadcrumbs(); ?>
        <h1><?php _e('Malls in', 'mallfinder'); ?> <?php the_title(); ?></h1>
        <?php if ($state_id) : ?>
            <p><?php _e('State:', 'mallfinder'); ?> <a href="<?php echo get_permalink($state_id); ?>"><?php echo get_the_title($state_id); ?></a></p>
        <?php endif; ?>
    </div>
</div>

<div class="section">
    <div class="container">
        <?php the_content(); ?>
        
        <?php
        // Get areas in this city
        $areas = get_posts(array(
            'post_type'      => 'area',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_key'       => '_area_city',
            'meta_value'     => $city_id,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));
        
        if ($areas) :
        ?>
            <h2 class="section-title"><?php _e('Areas', 'mallfinder'); ?></h2>
            <div class="cards-grid" style="margin-bottom: 3rem;">
                <?php foreach ($areas as $area) : ?>
                    <?php $mall_count = mallfinder_get_mall_count_by_location($area->ID, 'area'); ?>
                    <article class="card">
                        <div class="card-body">
                            <h3 class="card-title">
                                <a href="<?php echo get_permalink($area->ID); ?>"><?php echo esc_html($area->post_title); ?></a>
                            </h3>
                            <span style="color: var(--text-secondary); font-size: 0.875rem;"><?php echo $mall_count; ?> <?php _e('malls', 'mallfinder'); ?></span>
                            <div style="margin-top: 1rem;">
                                <a href="<?php echo get_permalink($area->ID); ?>" class="btn btn-outline btn-sm"><?php _e('View Malls', 'mallfinder'); ?></a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php
        // Get malls in this city
        $malls = get_posts(array(
            'post_type'      => 'mall',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
            'meta_key'       => '_mall_city',
            'meta_value'     => $city_id,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));
        
        if ($malls) :
        ?>
            <h2 class="section-title"><?php _e('All Malls', 'mallfinder'); ?></h2>
            <div class="cards-grid">
                <?php foreach ($malls as $mall) : ?>
                    <?php echo mallfinder_get_mall_card($mall->ID); ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-center" style="padding: 2rem;">
                <p><?php _e('No malls listed in this city yet.', 'mallfinder'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
endwhile;

get_footer();

<?php
/**
 * Single Area Template
 *
 * @package MallFinder
 */

get_header();

while (have_posts()) : the_post();
    $area_id = get_the_ID();
    $city_id = get_post_meta($area_id, '_area_city', true);
    $state_id = $city_id ? get_post_meta($city_id, '_city_state', true) : null;
?>

<div class="page-header">
    <div class="container">
        <?php mallfinder_breadcrumbs(); ?>
        <h1><?php _e('Malls in', 'mallfinder'); ?> <?php the_title(); ?></h1>
    </div>
</div>

<div class="section">
    <div class="container">
        <?php if ($city_id || $state_id) : ?>
            <p>
                <?php if ($city_id) : ?>
                    <?php _e('City:', 'mallfinder'); ?> <a href="<?php echo get_permalink($city_id); ?>"><?php echo get_the_title($city_id); ?></a>
                <?php endif; ?>
                <?php if ($state_id) : ?>
                    | <?php _e('State:', 'mallfinder'); ?> <a href="<?php echo get_permalink($state_id); ?>"><?php echo get_the_title($state_id); ?></a>
                <?php endif; ?>
            </p>
        <?php endif; ?>
        
        <?php the_content(); ?>
        
        <?php
        // Get malls in this area
        $malls = get_posts(array(
            'post_type'      => 'mall',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
            'meta_key'       => '_mall_area',
            'meta_value'     => $area_id,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));
        
        if ($malls) :
        ?>
            <h2 class="section-title"><?php _e('Malls in this Area', 'mallfinder'); ?></h2>
            <div class="cards-grid">
                <?php foreach ($malls as $mall) : ?>
                    <?php echo mallfinder_get_mall_card($mall->ID); ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-center" style="padding: 2rem;">
                <p><?php _e('No malls listed in this area yet.', 'mallfinder'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
endwhile;

get_footer();

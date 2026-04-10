<?php
/**
 * Single State Template
 *
 * @package MallFinder
 */

get_header();

while (have_posts()) : the_post();
    $state_id = get_the_ID();
    $image = get_the_post_thumbnail_url($state_id, 'mall-hero');
?>

<div class="page-header">
    <div class="container">
        <?php mallfinder_breadcrumbs(); ?>
        <h1><?php _e('Malls in', 'mallfinder'); ?> <?php the_title(); ?></h1>
        <?php the_content(); ?>
    </div>
</div>

<div class="section">
    <div class="container">
        <?php
        // Get cities in this state
        $cities = get_posts(array(
            'post_type'      => 'city',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_key'       => '_city_state',
            'meta_value'     => $state_id,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ));
        
        if ($cities) :
        ?>
            <h2 class="section-title"><?php _e('Cities', 'mallfinder'); ?></h2>
            <div class="cards-grid" style="margin-bottom: 3rem;">
                <?php foreach ($cities as $city) : ?>
                    <?php $mall_count = mallfinder_get_mall_count_by_location($city->ID, 'city'); ?>
                    <article class="card">
                        <div class="card-body">
                            <h3 class="card-title">
                                <a href="<?php echo get_permalink($city->ID); ?>"><?php echo esc_html($city->post_title); ?></a>
                            </h3>
                            <span style="color: var(--text-secondary); font-size: 0.875rem;"><?php echo $mall_count; ?> <?php _e('malls', 'mallfinder'); ?></span>
                            <div style="margin-top: 1rem;">
                                <a href="<?php echo get_permalink($city->ID); ?>" class="btn btn-outline btn-sm"><?php _e('View Malls', 'mallfinder'); ?></a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php
        // Get malls in this state
        $malls = get_posts(array(
            'post_type'      => 'mall',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
            'meta_key'       => '_mall_state',
            'meta_value'     => $state_id,
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
                <p><?php _e('No malls listed in this state yet.', 'mallfinder'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
endwhile;

get_footer();

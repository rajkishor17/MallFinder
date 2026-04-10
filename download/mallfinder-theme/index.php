<?php
/**
 * Main Template File
 *
 * @package MallFinder
 */

get_header();

// Get all malls for stats
$all_malls = get_posts(array(
    'post_type'      => 'mall',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
));

$total_malls = count($all_malls);
$existing_malls = 0;
$upcoming_malls = 0;

foreach ($all_malls as $mall) {
    $category = get_post_meta($mall->ID, '_mall_category', true);
    if ($category === 'upcoming') {
        $upcoming_malls++;
    } else {
        $existing_malls++;
    }
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1><?php _e('Find Your Perfect Shopping Destination', 'mallfinder'); ?></h1>
        <p><?php _e('Explore the best shopping malls in your area.', 'mallfinder'); ?></p>

        <!-- Quick Stats -->
        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-value"><?php echo esc_html($total_malls); ?></div>
                <div class="stat-label"><?php _e('Shopping Malls', 'mallfinder'); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo esc_html($existing_malls); ?></div>
                <div class="stat-label"><?php _e('Existing Malls', 'mallfinder'); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo esc_html($upcoming_malls); ?></div>
                <div class="stat-label"><?php _e('Upcoming Malls', 'mallfinder'); ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="main-content container">
    <!-- Search Box -->
    <div class="search-box">
        <form class="search-row" method="GET" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="search-input-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="s" class="search-input" placeholder="<?php esc_attr_e('Search malls...', 'mallfinder'); ?>" value="<?php echo esc_attr(isset($_GET['s']) ? $_GET['s'] : ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary">
                <?php _e('Search', 'mallfinder'); ?>
            </button>
        </form>
    </div>

    <!-- Map Section -->
    <?php if ($total_malls > 0 && function_exists('mallfinder_render_map')) : ?>
    <div class="map-section" style="margin-top: 1.5rem;">
        <div class="map-header">
            <div class="map-header-left">
                <div class="icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; margin: 0;"><?php _e('Explore Malls on Map', 'mallfinder'); ?></h3>
                    <p style="font-size: 0.75rem; color: var(--text-secondary); margin: 0;"><?php _e('Click on markers to view mall details', 'mallfinder'); ?></p>
                </div>
            </div>
            <div class="map-legend">
                <span class="map-legend-item"><span class="map-legend-dot existing"></span> <?php _e('Existing', 'mallfinder'); ?></span>
                <span class="map-legend-item"><span class="map-legend-dot upcoming"></span> <?php _e('Upcoming', 'mallfinder'); ?></span>
            </div>
        </div>
        <?php
        $mall_ids = array();
        foreach ($all_malls as $mall) {
            $mall_ids[] = $mall->ID;
        }
        echo mallfinder_render_map(array('id' => 'home-map', 'height' => '400px', 'malls' => $mall_ids));
        ?>
    </div>
    <?php endif; ?>

    <!-- Malls Section -->
    <section class="section" style="padding-top: 2rem;">
        <div class="section-header">
            <div>
                <h2 class="section-title"><?php _e('All Shopping Malls', 'mallfinder'); ?></h2>
                <p class="section-subtitle"><?php printf(_n('%s mall found', '%s malls found', $total_malls, 'mallfinder'), number_format_i18n($total_malls)); ?></p>
            </div>
        </div>

        <?php
        $featured_malls = get_posts(array(
            'post_type'      => 'mall',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        if ($featured_malls) :
        ?>
            <div class="cards-grid">
                <?php foreach ($featured_malls as $mall) : ?>
                    <?php
                    $mall_id = $mall->ID;
                    $image = get_the_post_thumbnail_url($mall_id, 'mall-thumbnail');
                    if (!$image) {
                        $image = get_template_directory_uri() . '/assets/images/default-mall.jpg';
                    }

                    $category = get_post_meta($mall_id, '_mall_category', true);
                    $is_upcoming = ($category === 'upcoming');
                    $address = mallfinder_format_address($mall_id);
                    $stores_count = mallfinder_get_stores_count($mall_id);
                    ?>
                    <article class="mall-card <?php echo $is_upcoming ? 'upcoming' : ''; ?>">
                        <a href="<?php echo get_permalink($mall_id); ?>">
                            <div class="mall-card-image">
                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($mall->post_title); ?>">
                                <div class="mall-card-badge">
                                    <span class="badge <?php echo $is_upcoming ? 'badge-upcoming' : 'badge-existing'; ?>">
                                        <?php echo $is_upcoming ? __('Upcoming', 'mallfinder') : __('Existing', 'mallfinder'); ?>
                                    </span>
                                    <h3 class="mall-card-title"><?php echo esc_html($mall->post_title); ?></h3>
                                </div>
                            </div>
                        </a>
                        <div class="mall-card-body">
                            <div class="mall-card-meta">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span><?php echo esc_html(wp_trim_words($address, 10)); ?></span>
                            </div>

                            <div class="mall-card-footer">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                <span><?php echo esc_html($stores_count); ?> <?php _e('Stores', 'mallfinder'); ?></span>
                                <span style="margin-left: auto;">
                                    <a href="<?php echo get_permalink($mall_id); ?>" class="btn btn-sm <?php echo $is_upcoming ? 'btn-secondary' : 'btn-primary'; ?>"><?php _e('View Details', 'mallfinder'); ?></a>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="text-center" style="padding: 3rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; color: var(--text-muted); margin: 0 auto 1rem;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <h3><?php _e('No malls found', 'mallfinder'); ?></h3>
                <p><?php _e('Check back later for mall listings.', 'mallfinder'); ?></p>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php get_footer(); ?>

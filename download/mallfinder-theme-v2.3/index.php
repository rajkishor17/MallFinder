<?php
/**
 * Main Template File (Home Page)
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

// Handle search and filter
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$category_filter = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';

// Build query args
$malls_args = array(
    'post_type'      => 'mall',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
    'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
);

$meta_query = array();

if ($category_filter !== 'all') {
    $meta_query[] = array(
        'key'     => '_mall_category',
        'value'   => $category_filter,
        'compare' => '=',
    );
}

if (!empty($meta_query)) {
    $malls_args['meta_query'] = $meta_query;
}

if ($search_query) {
    $malls_args['s'] = $search_query;
}

$malls_query = new WP_Query($malls_args);

// Sidebar ad settings
$left_sidebar_enabled = get_option('mallfinder_left_sidebar_enable');
$right_sidebar_enabled = get_option('mallfinder_right_sidebar_enable');
$left_ad_type = get_option('mallfinder_left_ad_type', 'image');
$right_ad_type = get_option('mallfinder_right_ad_type', 'image');
$left_ad_image = get_option('mallfinder_left_ad_image');
$left_ad_url = get_option('mallfinder_left_ad_url');
$left_ad_code = get_option('mallfinder_left_ad_code');
$right_ad_image = get_option('mallfinder_right_ad_image');
$right_ad_url = get_option('mallfinder_right_ad_url');
$right_ad_code = get_option('mallfinder_right_ad_code');
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

        <!-- Social Share Buttons -->
        <div class="hero-social-share">
            <div class="social-share-buttons">
                <button class="social-share-btn facebook" onclick="shareOnFacebook()" aria-label="Share on Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </button>
                <button class="social-share-btn twitter" onclick="shareOnTwitter()" aria-label="Share on Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </button>
                <button class="social-share-btn whatsapp" onclick="shareOnWhatsApp()" aria-label="Share on WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </button>
                <button class="social-share-btn copy-link" onclick="copyLink(this)" aria-label="Copy link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="home-wrapper">
    <!-- Left Sidebar -->
    <?php if ($left_sidebar_enabled) : ?>
    <aside class="sidebar sidebar-left">
        <div class="sidebar-ad">
            <?php if ($left_ad_type === 'image' && $left_ad_image) : ?>
                <?php if ($left_ad_url) : ?>
                    <a href="<?php echo esc_url($left_ad_url); ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url($left_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url($left_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                <?php endif; ?>
            <?php elseif ($left_ad_type === 'google' && $left_ad_code) : ?>
                <div class="sidebar-ad-code">
                    <?php echo $left_ad_code; ?>
                </div>
            <?php else : ?>
                <div class="sidebar-ad-placeholder">
                    <span><?php _e('Ad Space', 'mallfinder'); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </aside>
    <?php endif; ?>

    <!-- Main Content Area -->
    <main class="home-main-content">
    <div class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <!-- Search Box -->
    <div class="search-box">
        <form class="search-form" method="GET" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="search-main-row">
                <div class="search-input-wrapper" style="position: relative;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" name="s" id="mall-search-input" class="search-input" placeholder="<?php esc_attr_e('Search malls by name, state, city, area, or address...', 'mallfinder'); ?>" value="<?php echo esc_attr($search_query); ?>" autocomplete="off">
                    <div id="search-suggestions" class="search-suggestions"></div>
                </div>
                <input type="hidden" name="post_type" value="mall">
                <button type="submit" class="btn btn-primary" id="search-btn">
                    <?php _e('Search', 'mallfinder'); ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <div class="map-header">
            <div class="map-header-left">
                <div class="icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <div>
                    <h3 style="font-size: 1rem; margin: 0;"><?php _e('Explore Malls on Map', 'mallfinder'); ?></h3>
                    <p style="font-size: 0.75rem; color: var(--slate-500); margin: 0;"><?php _e('Click on markers to view mall details', 'mallfinder'); ?></p>
                </div>
            </div>
            <div class="map-legend">
                <span class="map-legend-item"><span class="map-legend-dot existing"></span> <?php _e('Existing', 'mallfinder'); ?></span>
                <span class="map-legend-item"><span class="map-legend-dot upcoming"></span> <?php _e('Upcoming', 'mallfinder'); ?></span>
            </div>
        </div>
        <div class="map-container">
            <div id="home-map"></div>
        </div>
    </div>

    <!-- Category Filter Buttons -->
    <div class="category-filter-buttons">
        <button type="button" class="category-btn category-btn-all <?php echo $category_filter === 'all' ? 'active' : ''; ?>" data-category="all">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M3 21h18"></path><path d="M5 21V7l8-4v18"></path><path d="M19 21V11l-6-4"></path></svg>
            <?php _e('All Malls', 'mallfinder'); ?>
            <span class="category-count"><?php echo esc_html($total_malls); ?></span>
        </button>
        <button type="button" class="category-btn category-btn-existing <?php echo $category_filter === 'existing' ? 'active' : ''; ?>" data-category="existing">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            <?php _e('Existing Malls', 'mallfinder'); ?>
            <span class="category-count"><?php echo esc_html($existing_malls); ?></span>
        </button>
        <button type="button" class="category-btn category-btn-upcoming <?php echo $category_filter === 'upcoming' ? 'active' : ''; ?>" data-category="upcoming">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;"><path d="M12 3l1.912 5.813L20 10l-6.088 1.187L12 17l-1.912-5.813L4 10l6.088-1.187L12 3z"></path></svg>
            <?php _e('Upcoming Malls', 'mallfinder'); ?>
            <span class="category-count"><?php echo esc_html($upcoming_malls); ?></span>
        </button>
    </div>

    <!-- Malls Section -->
    <section class="section" id="malls-section">
        <div class="section-header">
            <div>
                <h2 class="section-title"><?php _e('Browse Shopping Malls', 'mallfinder'); ?></h2>
                <p class="section-subtitle">
                    <?php
                    printf(
                        _n('%s mall found', '%s malls found', $malls_query->found_posts, 'mallfinder'),
                        number_format_i18n($malls_query->found_posts)
                    );
                    ?>
                </p>
            </div>
        </div>

        <?php if ($malls_query->have_posts()) : ?>
            <div class="cards-grid">
                <?php while ($malls_query->have_posts()) : $malls_query->the_post(); ?>
                    <?php
                    $mall_id = get_the_ID();
                    $image = mallfinder_get_mall_image($mall_id);
                    $category = get_post_meta($mall_id, '_mall_category', true);
                    $is_upcoming = ($category === 'upcoming');
                    $address = mallfinder_format_address($mall_id);
                    $stores_count = mallfinder_get_mall_stores_count($mall_id);
                    $opening_hours = get_post_meta($mall_id, '_mall_opening_hours', true);
                    ?>
                    <article class="mall-card <?php echo $is_upcoming ? 'upcoming' : ''; ?>">
                        <a href="<?php the_permalink(); ?>">
                            <div class="mall-card-image">
                                <img src="<?php echo esc_url($image); ?>" alt="<?php the_title_attribute(); ?>">
                                <div class="mall-card-badge">
                                    <span class="badge <?php echo $is_upcoming ? 'badge-upcoming' : 'badge-existing'; ?>">
                                        <?php if ($is_upcoming) : ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><path d="M12 3l1.912 5.813L20 10l-6.088 1.187L12 17l-1.912-5.813L4 10l6.088-1.187L12 3z"></path></svg>
                                            <?php _e('Upcoming', 'mallfinder'); ?>
                                        <?php else : ?>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 12px; height: 12px;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                            <?php _e('Existing', 'mallfinder'); ?>
                                        <?php endif; ?>
                                    </span>
                                    <h3 class="mall-card-title"><?php the_title(); ?></h3>
                                </div>
                            </div>
                        </a>
                        <div class="mall-card-body">
                            <div class="mall-card-meta">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span><?php echo esc_html(wp_trim_words($address, 10)); ?></span>
                            </div>

                            <?php if ($opening_hours && !$is_upcoming) : ?>
                                <div class="mall-card-meta">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <span><?php echo esc_html($opening_hours); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="mall-card-footer">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
                                <span><?php echo esc_html($stores_count); ?> <?php _e('Stores', 'mallfinder'); ?></span>
                                <span style="margin-left: auto;">
                                    <a href="<?php the_permalink(); ?>" class="btn btn-sm <?php echo $is_upcoming ? 'btn-secondary' : 'btn-primary'; ?>"><?php _e('View Details', 'mallfinder'); ?></a>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if ($malls_query->max_num_pages > 1) : ?>
                <div class="pagination">
                    <?php
                    echo paginate_links(array(
                        'total'     => $malls_query->max_num_pages,
                        'current'   => max(1, get_query_var('paged')),
                        'prev_text' => __('&laquo; Previous', 'mallfinder'),
                        'next_text' => __('Next &raquo;', 'mallfinder'),
                    ));
                    ?>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="text-center" style="padding: 3rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; color: var(--slate-400); margin: 0 auto 1rem;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <h3><?php _e('No malls found', 'mallfinder'); ?></h3>
                <p><?php _e('Check back later for mall listings.', 'mallfinder'); ?></p>
            </div>
        <?php endif; ?>
    </section>
    </div><!-- End container -->
    </main><!-- End home-main-content -->

    <!-- Right Sidebar -->
    <?php if ($right_sidebar_enabled) : ?>
    <aside class="sidebar sidebar-right">
        <div class="sidebar-ad">
            <?php if ($right_ad_type === 'image' && $right_ad_image) : ?>
                <?php if ($right_ad_url) : ?>
                    <a href="<?php echo esc_url($right_ad_url); ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url($right_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url($right_ad_image); ?>" alt="<?php esc_attr_e('Advertisement', 'mallfinder'); ?>" class="sidebar-ad-image">
                <?php endif; ?>
            <?php elseif ($right_ad_type === 'google' && $right_ad_code) : ?>
                <div class="sidebar-ad-code">
                    <?php echo $right_ad_code; ?>
                </div>
            <?php else : ?>
                <div class="sidebar-ad-placeholder">
                    <span><?php _e('Ad Space', 'mallfinder'); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </aside>
    <?php endif; ?>
</div><!-- End home-wrapper -->

<!-- Explore Malls Section (Featured Malls Carousel) -->
<?php
$featured_malls = get_posts(array(
    'post_type'      => 'mall',
    'posts_per_page' => 10,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
));

if (!empty($featured_malls)) :
?>
<section class="explore-malls-section">
    <div class="container">
        <div class="explore-section-header">
            <h2 class="explore-section-title"><?php _e('Explore Other Malls', 'mallfinder'); ?></h2>
            <p class="explore-section-subtitle"><?php _e('Discover more shopping destinations near you', 'mallfinder'); ?></p>
        </div>

        <div class="explore-malls-carousel-wrapper">
            <button class="carousel-btn carousel-btn-prev" id="home-carousel-prev" aria-label="Previous">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </button>

            <div class="explore-malls-carousel" id="home-explore-carousel">
                <?php foreach ($featured_malls as $featured_mall) : ?>
                    <?php
                    $featured_image = mallfinder_get_mall_image($featured_mall->ID);
                    $featured_category = get_post_meta($featured_mall->ID, '_mall_category', true);
                    $featured_is_upcoming = ($featured_category === 'upcoming');
                    $featured_address = mallfinder_format_address($featured_mall->ID);
                    ?>
                    <a href="<?php echo esc_url(get_permalink($featured_mall->ID)); ?>" class="explore-mall-card <?php echo $featured_is_upcoming ? 'upcoming' : ''; ?>">
                        <div class="explore-mall-image">
                            <?php if ($featured_image) : ?>
                                <img src="<?php echo esc_url($featured_image); ?>" alt="<?php echo esc_attr($featured_mall->post_title); ?>">
                            <?php else : ?>
                                <div class="explore-mall-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 21h18"></path>
                                        <path d="M5 21V7l8-4v18"></path>
                                        <path d="M19 21V11l-6-4"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <span class="explore-mall-badge <?php echo $featured_is_upcoming ? 'badge-upcoming' : 'badge-existing'; ?>">
                                <?php echo $featured_is_upcoming ? __('Upcoming', 'mallfinder') : __('Existing', 'mallfinder'); ?>
                            </span>
                        </div>
                        <div class="explore-mall-content">
                            <h3><?php echo esc_html($featured_mall->post_title); ?></h3>
                            <p><?php echo esc_html(wp_trim_words($featured_address, 8)); ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <button class="carousel-btn carousel-btn-next" id="home-carousel-next" aria-label="Next">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </button>
        </div>

        <div class="explore-section-footer">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary"><?php _e('View All Malls', 'mallfinder'); ?></a>
        </div>
    </div>
</section>

<script>
(function() {
    var carousel = document.getElementById('home-explore-carousel');
    var prevBtn = document.getElementById('home-carousel-prev');
    var nextBtn = document.getElementById('home-carousel-next');

    if (carousel && prevBtn && nextBtn) {
        var scrollAmount = 300;

        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });
    }
})();
</script>
<?php endif; ?>

<!-- Map Data -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php
    $map_malls = array();
    foreach ($all_malls as $mall) {
        $lat = get_post_meta($mall->ID, '_mall_latitude', true);
        $lng = get_post_meta($mall->ID, '_mall_longitude', true);
        $category = get_post_meta($mall->ID, '_mall_category', true);

        if ($lat && $lng) {
            $map_malls[] = array(
                'id'        => $mall->ID,
                'title'     => $mall->post_title,
                'permalink' => get_permalink($mall->ID),
                'lat'       => floatval($lat),
                'lng'       => floatval($lng),
                'address'   => mallfinder_format_address($mall->ID),
                'image'     => get_the_post_thumbnail_url($mall->ID, 'mall-thumbnail'),
                'category'  => $category ?: 'existing',
            );
        }
    }
    ?>

    var mallsData = <?php echo json_encode($map_malls); ?>;

    if (typeof L !== 'undefined' && mallsData.length > 0) {
        var map = L.map('home-map').setView([
            <?php echo esc_js(mallfinder_get_setting('default_lat', '40.7128')); ?>,
            <?php echo esc_js(mallfinder_get_setting('default_lng', '-74.0060')); ?>
        ], <?php echo esc_js(mallfinder_get_setting('default_zoom', '12')); ?>);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var bounds = [];

        mallsData.forEach(function(mall) {
            var color = mall.category === 'upcoming' ? '#f59e0b' : '#10b981';
            var marker = L.circleMarker([mall.lat, mall.lng], {
                radius: 10,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(map);

            var popupContent = '<div style="min-width: 200px;">';
            if (mall.image) {
                popupContent += '<img src="' + mall.image + '" style="width: 100%; height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">';
            }
            popupContent += '<h4 style="margin: 0 0 4px; font-size: 14px;"><a href="' + mall.permalink + '">' + mall.title + '</a></h4>';
            popupContent += '<p style="margin: 0; font-size: 12px; color: #64748b;">' + mall.address + '</p>';
            popupContent += '<a href="' + mall.permalink + '" style="display: inline-block; margin-top: 8px; padding: 4px 12px; background: linear-gradient(to right, #10b981, #14b8a6); color: white; border-radius: 6px; font-size: 12px; text-decoration: none;">View Details</a>';
            popupContent += '</div>';

            marker.bindPopup(popupContent);
            bounds.push([mall.lat, mall.lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
});
</script>

<!-- Category Filter Buttons Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var categoryButtons = document.querySelectorAll('.category-btn');

    // Scroll to malls section if category filter is active
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('category') || urlParams.has('s')) {
        var mallsSection = document.getElementById('malls-section');
        if (mallsSection) {
            setTimeout(function() {
                mallsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }

    categoryButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var category = this.getAttribute('data-category');

            // Build the URL with the selected category
            var url = window.location.origin + window.location.pathname;
            var params = new URLSearchParams();

            // Always add post_type
            params.set('post_type', 'mall');

            if (category !== 'all') {
                params.set('category', category);
            }

            // Build final URL with hash to scroll to results
            url += '?' + params.toString() + '#malls-section';

            window.location.href = url;
        });
    });
});
</script>

<!-- Search Autocomplete Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('mall-search-input');
    var suggestionsBox = document.getElementById('search-suggestions');
    var searchTimeout = null;

    if (searchInput && suggestionsBox) {
        searchInput.addEventListener('input', function() {
            var searchTerm = this.value.trim();

            clearTimeout(searchTimeout);

            if (searchTerm.length < 2) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(function() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '<?php echo admin_url('admin-ajax.php'); ?>?action=mallfinder_search&term=' + encodeURIComponent(searchTerm) + '&nonce=<?php echo wp_create_nonce('mallfinder_nonce'); ?>', true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            var results = JSON.parse(xhr.responseText);
                            if (results.length > 0) {
                                var html = '';
                                results.forEach(function(mall) {
                                    var badgeClass = mall.category === 'upcoming' ? 'badge-upcoming' : 'badge-existing';
                                    var badgeText = mall.category === 'upcoming' ? 'Upcoming' : 'Existing';
                                    html += '<a href="' + mall.url + '" class="suggestion-item">';
                                    html += '<img src="' + mall.image + '" alt="' + mall.title + '" class="suggestion-image">';
                                    html += '<div class="suggestion-content">';
                                    html += '<div class="suggestion-title">' + mall.title + '</div>';
                                    html += '<div class="suggestion-address">' + mall.address + '</div>';
                                    html += '</div>';
                                    html += '<span class="suggestion-badge ' + badgeClass + '">' + badgeText + '</span>';
                                    html += '</a>';
                                });
                                suggestionsBox.innerHTML = html;
                                suggestionsBox.style.display = 'block';
                            } else {
                                suggestionsBox.innerHTML = '<div class="suggestion-empty">No malls found</div>';
                                suggestionsBox.style.display = 'block';
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                        }
                    }
                };
                xhr.send();
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });

        // Show suggestions on focus if there's content
        searchInput.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                this.dispatchEvent(new Event('input'));
            }
        });
    }
});
</script>

<?php get_footer(); ?>

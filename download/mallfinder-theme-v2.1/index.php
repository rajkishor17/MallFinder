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
        <?php echo mallfinder_social_share_buttons(__('MallFinder - Find Your Perfect Shopping Destination', 'mallfinder'), home_url('/'), 'hero'); ?>
    </div>
</section>

<!-- Main Content -->
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
</div>

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
            <h2 class="explore-section-title"><?php _e('Explore Shopping Malls', 'mallfinder'); ?></h2>
            <p class="explore-section-subtitle"><?php _e('Discover the best shopping destinations', 'mallfinder'); ?></p>
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

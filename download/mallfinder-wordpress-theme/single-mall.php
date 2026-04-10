<?php
/**
 * Single Mall Template
 * 
 * @package MallFinder
 */

get_header();

$mall_id = get_the_ID();
$status = mallfinder_get_mall_status($mall_id);
$address = mallfinder_get_mall_address($mall_id);
$phone = mallfinder_get_mall_phone($mall_id);
$website = mallfinder_get_mall_website($mall_id);
$opening_hours = mallfinder_get_mall_opening_hours($mall_id);
$expected_opening = mallfinder_get_mall_expected_opening($mall_id);
$coordinates = mallfinder_get_mall_coordinates($mall_id);
$location = mallfinder_get_mall_location($mall_id);
$amenities = mallfinder_get_mall_amenities($mall_id);
$features = mallfinder_get_mall_features($mall_id);
?>

<!-- Hero Section -->
<div class="mall-hero">
    <?php if (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail('full', array('style' => 'object-fit: cover;')); ?>
    <?php endif; ?>
    <div class="mall-hero-content">
        <div class="container">
            <h1><?php the_title(); ?></h1>
            <div class="mall-hero-meta">
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?php echo esc_html(implode(', ', array_filter(array($location['area'] ?? '', $location['city'] ?? '', $location['state'] ?? '')))); ?>
                </span>
                <?php if ($opening_hours) : ?>
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?php echo esc_html($opening_hours); ?>
                    </span>
                <?php endif; ?>
                <span class="card-badge <?php echo esc_attr($status); ?>">
                    <?php echo esc_html($status === 'upcoming' ? __('Upcoming', 'mallfinder') : __('Open', 'mallfinder')); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<main class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <!-- Breadcrumb -->
    <nav style="margin-bottom: 1.5rem; font-size: 0.875rem; color: var(--color-slate-500);">
        <a href="<?php echo esc_url(home_url('/')); ?>" style="color: var(--color-slate-500);"><?php _e('Home', 'mallfinder'); ?></a> /
        <a href="<?php echo esc_url(get_post_type_archive_link('mall')); ?>" style="color: var(--color-slate-500);"><?php _e('Malls', 'mallfinder'); ?></a> /
        <span style="color: var(--color-slate-900);"><?php the_title(); ?></span>
    </nav>
    
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem;">
        <div>
            <!-- Mall Details -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-content" style="padding: 1.5rem;">
                    <h2 style="margin-bottom: 1rem;"><?php _e('About', 'mallfinder'); ?></h2>
                    <?php the_content(); ?>
                </div>
            </div>
            
            <!-- Map -->
            <?php if ($coordinates['lat'] && $coordinates['lng']) : ?>
                <div class="card" style="margin-bottom: 1.5rem; overflow: hidden;">
                    <div id="single-mall-map" class="map-container" style="height: 300px; margin: 0;"></div>
                </div>
            <?php endif; ?>
            
            <!-- Store Directory -->
            <?php
            $stores_args = array(
                'post_type'      => 'store',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'meta_query'     => array(
                    array(
                        'key'     => '_store_mall_id',
                        'value'   => $mall_id,
                        'compare' => '=',
                    ),
                ),
            );
            $stores_query = new WP_Query($stores_args);
            
            if ($stores_query->have_posts()) :
            ?>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-content" style="padding: 1.5rem;">
                        <h2 style="margin-bottom: 1rem;">
                            <?php _e('Store Directory', 'mallfinder'); ?>
                            <span style="font-size: 0.875rem; font-weight: 400; color: var(--color-slate-500);">
                                (<?php echo $stores_query->found_posts; ?> <?php _e('stores', 'mallfinder'); ?>)
                            </span>
                        </h2>
                        
                        <!-- Store Filter -->
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                            <input type="text" id="store-search" class="form-input" placeholder="<?php esc_attr_e('Search stores...', 'mallfinder'); ?>" style="flex: 1;" />
                            <select id="store-category-filter" class="form-select">
                                <option value=""><?php _e('All Categories', 'mallfinder'); ?></option>
                                <?php foreach (mallfinder_get_store_categories() as $category) : ?>
                                    <option value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html($category->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="store-grid" id="store-grid">
                            <?php while ($stores_query->have_posts()) : $stores_query->the_post(); ?>
                                <?php get_template_part('templates/store', 'card'); ?>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar -->
        <div>
            <!-- Mall Info -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-content" style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 1rem;"><?php _e('Information', 'mallfinder'); ?></h3>
                    
                    <?php if ($address) : ?>
                        <p style="margin-bottom: 0.75rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.5rem; color: var(--color-emerald-500);"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?php echo esc_html($address); ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($phone) : ?>
                        <p style="margin-bottom: 0.75rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.5rem; color: var(--color-emerald-500);"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+\-]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($website) : ?>
                        <p style="margin-bottom: 0.75rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.5rem; color: var(--color-emerald-500);"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener noreferrer"><?php _e('Visit Website', 'mallfinder'); ?></a>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($expected_opening && $status === 'upcoming') : ?>
                        <p style="margin-bottom: 0.75rem; color: var(--color-amber-600);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; vertical-align: middle; margin-right: 0.5rem;"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <?php _e('Expected Opening:', 'mallfinder'); ?> <?php echo esc_html(mallfinder_format_date($expected_opening, 'F Y')); ?>
                        </p>
                    <?php endif; ?>
                    
                    <!-- Social Share -->
                    <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-slate-200);">
                        <?php mallfinder_social_share(); ?>
                    </div>
                </div>
            </div>
            
            <!-- Amenities -->
            <?php if (!empty($amenities) && !is_wp_error($amenities)) : ?>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div class="card-content" style="padding: 1.5rem;">
                        <h3 style="margin-bottom: 1rem;"><?php _e('Amenities', 'mallfinder'); ?></h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            <?php foreach ($amenities as $amenity) : ?>
                                <span style="padding: 0.25rem 0.75rem; background: var(--color-slate-100); border-radius: 9999px; font-size: 0.875rem;">
                                    <?php echo esc_html($amenity->name); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php if ($coordinates['lat'] && $coordinates['lng']) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('single-mall-map').setView([<?php echo esc_js($coordinates['lat']); ?>, <?php echo esc_js($coordinates['lng']); ?>], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    var markerColor = '<?php echo $status === "upcoming" ? "#f59e0b" : "#10b981"; ?>';
    var marker = L.circleMarker([<?php echo esc_js($coordinates['lat']); ?>, <?php echo esc_js($coordinates['lng']); ?>], {
        radius: 10,
        fillColor: markerColor,
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.8
    }).addTo(map);
    
    marker.bindPopup('<strong><?php echo esc_js(get_the_title()); ?></strong>');
});
</script>
<?php endif; ?>

<?php get_footer(); ?>

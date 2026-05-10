<?php
/**
 * Front Page Template
 * 
 * @package MallFinder
 */

get_header();

// Get statistics
$total_malls = wp_count_posts('mall')->publish;
$existing_malls = mallfinder_get_mall_count('existing');
$upcoming_malls = mallfinder_get_mall_count('upcoming');
?>

<section class="hero-section">
    <div class="container">
        <h1><?php _e('Find Your Perfect Shopping Destination', 'mallfinder'); ?></h1>
        <p><?php _e('Explore the best shopping malls in your area.', 'mallfinder'); ?></p>
        
        <div class="stats-grid">
            <div class="stat-item">
                <div class="number"><?php echo esc_html($total_malls); ?></div>
                <div class="label"><?php _e('Shopping Malls', 'mallfinder'); ?></div>
            </div>
            <div class="stat-item">
                <div class="number"><?php echo esc_html($existing_malls); ?></div>
                <div class="label"><?php _e('Existing Malls', 'mallfinder'); ?></div>
            </div>
            <div class="stat-item">
                <div class="number"><?php echo esc_html($upcoming_malls); ?></div>
                <div class="label"><?php _e('Upcoming Malls', 'mallfinder'); ?></div>
            </div>
        </div>
        
        <div style="margin-top: 1rem; display: flex; justify-content: center;">
            <?php mallfinder_social_share(array('title' => mallfinder_get_site_name() . ' - ' . mallfinder_get_site_tagline())); ?>
        </div>
    </div>
</section>

<main class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <!-- Search and Filter -->
    <div class="search-filter-bar">
        <div class="inner">
            <div class="search-wrapper">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="mall-search" class="form-input" placeholder="<?php esc_attr_e('Search malls by name, state, city, area...', 'mallfinder'); ?>" />
            </div>
            
            <select id="status-filter" class="form-select">
                <option value=""><?php _e('All Malls', 'mallfinder'); ?></option>
                <option value="existing"><?php _e('Existing Malls', 'mallfinder'); ?></option>
                <option value="upcoming"><?php _e('Upcoming Malls', 'mallfinder'); ?></option>
            </select>
            
            <select id="state-filter" class="form-select">
                <option value=""><?php _e('All States', 'mallfinder'); ?></option>
                <?php foreach (mallfinder_get_states() as $state) : ?>
                    <option value="<?php echo esc_attr($state->term_id); ?>"><?php echo esc_html($state->name); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select id="city-filter" class="form-select" disabled>
                <option value=""><?php _e('All Cities', 'mallfinder'); ?></option>
            </select>
        </div>
    </div>
    
    <!-- Map Section -->
    <div class="map-container" id="mall-map"></div>
    
    <!-- Mall Type Toggle -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <button class="btn btn-primary status-toggle active" data-status="all">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <?php _e('All Malls', 'mallfinder'); ?> (<?php echo $total_malls; ?>)
        </button>
        <button class="btn btn-outline status-toggle" data-status="upcoming">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            <?php _e('Upcoming Malls', 'mallfinder'); ?> (<?php echo $upcoming_malls; ?>)
        </button>
    </div>
    
    <!-- Malls Grid -->
    <div class="mall-grid" id="malls-grid">
        <?php
        $args = array(
            'post_type'      => 'mall',
            'post_status'    => 'publish',
            'posts_per_page' => 12,
            'paged'          => get_query_var('paged') ? get_query_var('paged') : 1,
        );
        
        $malls_query = new WP_Query($args);
        
        if ($malls_query->have_posts()) :
            while ($malls_query->have_posts()) : $malls_query->the_post();
                get_template_part('templates/mall', 'card');
            endwhile;
        else :
            ?>
            <div style="text-align: center; padding: 3rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; display: block; opacity: 0.5;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <h3><?php _e('No malls found', 'mallfinder'); ?></h3>
                <p style="color: var(--color-slate-500);"><?php _e('Try adjusting your search or filter criteria.', 'mallfinder'); ?></p>
            </div>
            <?php
        endif;
        wp_reset_postdata();
        ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($malls_query->max_num_pages > 1) : ?>
        <div class="pagination">
            <?php
            echo paginate_links(array(
                'total'     => $malls_query->max_num_pages,
                'current'   => max(1, get_query_var('paged')),
                'prev_text' => '← ' . __('Previous', 'mallfinder'),
                'next_text' => __('Next', 'mallfinder') . ' →',
            ));
            ?>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>

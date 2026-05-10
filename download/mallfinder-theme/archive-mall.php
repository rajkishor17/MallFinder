<?php
/**
 * Archive Mall Template
 *
 * @package MallFinder
 */

get_header();
?>

<div class="page-header">
    <div class="container">
        <?php mallfinder_breadcrumbs(); ?>
        <h1><?php _e('All Malls', 'mallfinder'); ?></h1>
        <p><?php _e('Browse our comprehensive directory of shopping malls.', 'mallfinder'); ?></p>
    </div>
</div>

<div class="section">
    <div class="container">
        <!-- Filter Bar -->
        <form class="filter-bar" method="GET" action="">
            <?php
            $states = get_posts(array('post_type' => 'state', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
            $selected_state = isset($_GET['state']) ? intval($_GET['state']) : 0;
            $selected_city = isset($_GET['city']) ? intval($_GET['city']) : 0;
            ?>
            <select name="state" id="filter-state">
                <option value=""><?php _e('All States', 'mallfinder'); ?></option>
                <?php foreach ($states as $state) : ?>
                    <option value="<?php echo $state->ID; ?>" <?php selected($selected_state, $state->ID); ?>>
                        <?php echo esc_html($state->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="city" id="filter-city" <?php echo !$selected_state ? 'disabled' : ''; ?>>
                <option value=""><?php _e('All Cities', 'mallfinder'); ?></option>
                <?php if ($selected_state) : ?>
                    <?php
                    $cities = get_posts(array(
                        'post_type'      => 'city',
                        'posts_per_page' => -1,
                        'meta_key'       => '_city_state',
                        'meta_value'     => $selected_state,
                        'orderby'        => 'title',
                        'order'          => 'ASC',
                    ));
                    foreach ($cities as $city) :
                    ?>
                        <option value="<?php echo $city->ID; ?>" <?php selected($selected_city, $city->ID); ?>>
                            <?php echo esc_html($city->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            
            <input type="text" name="s" placeholder="<?php esc_attr_e('Search malls...', 'mallfinder'); ?>" value="<?php echo esc_attr(isset($_GET['s']) ? $_GET['s'] : ''); ?>">
            
            <button type="submit" class="btn btn-primary"><?php _e('Filter', 'mallfinder'); ?></button>
            <a href="<?php echo get_post_type_archive_link('mall'); ?>" class="btn btn-outline"><?php _e('Reset', 'mallfinder'); ?></a>
        </form>
        
        <!-- Results Count -->
        <?php global $wp_query; ?>
        <p style="color: var(--text-secondary); margin-bottom: 1rem;">
            <?php printf(_n('%s mall found', '%s malls found', $wp_query->found_posts, 'mallfinder'), number_format_i18n($wp_query->found_posts)); ?>
        </p>
        
        <!-- Malls Grid -->
        <?php if (have_posts()) : ?>
            <div class="cards-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php echo mallfinder_get_mall_card(get_the_ID()); ?>
                <?php endwhile; ?>
            </div>
            
            <?php mallfinder_pagination(); ?>
            
        <?php else : ?>
            <div class="text-center" style="padding: 3rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; color: var(--text-secondary); margin: 0 auto 1rem;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <h3><?php _e('No malls found', 'mallfinder'); ?></h3>
                <p><?php _e('Try adjusting your search or filter criteria.', 'mallfinder'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#filter-state').on('change', function() {
        var stateId = $(this).val();
        var citySelect = $('#filter-city');
        
        citySelect.prop('disabled', !stateId);
        
        if (stateId) {
            $.ajax({
                url: mallfinderData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'mallfinder_get_cities_by_state',
                    nonce: mallfinderData.nonce,
                    state_id: stateId
                },
                success: function(response) {
                    if (response.success) {
                        citySelect.empty().append('<option value=""><?php _e('All Cities', 'mallfinder'); ?></option>');
                        response.data.cities.forEach(function(city) {
                            citySelect.append('<option value="' + city.id + '">' + city.title + '</option>');
                        });
                    }
                }
            });
        } else {
            citySelect.empty().append('<option value=""><?php _e('All Cities', 'mallfinder'); ?></option>');
        }
    });
});
</script>

<?php get_footer(); ?>

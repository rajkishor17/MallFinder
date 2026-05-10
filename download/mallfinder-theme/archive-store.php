<?php
/**
 * Archive Store Template
 *
 * @package MallFinder
 */

get_header();
?>

<div class="page-header">
    <div class="container">
        <?php mallfinder_breadcrumbs(); ?>
        <h1><?php _e('All Stores', 'mallfinder'); ?></h1>
        <p><?php _e('Browse stores across all malls.', 'mallfinder'); ?></p>
    </div>
</div>

<div class="section">
    <div class="container">
        <!-- Filter Bar -->
        <form class="filter-bar" method="GET" action="">
            <?php
            $mall_filter = isset($_GET['mall']) ? intval($_GET['mall']) : 0;
            $category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;
            $malls = get_posts(array('post_type' => 'mall', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
            $categories = get_terms(array('taxonomy' => 'store_category', 'hide_empty' => false));
            ?>
            <select name="mall">
                <option value=""><?php _e('All Malls', 'mallfinder'); ?></option>
                <?php foreach ($malls as $mall) : ?>
                    <option value="<?php echo $mall->ID; ?>" <?php selected($mall_filter, $mall->ID); ?>>
                        <?php echo esc_html($mall->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select name="category">
                <option value=""><?php _e('All Categories', 'mallfinder'); ?></option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo $cat->term_id; ?>" <?php selected($category_filter, $cat->term_id); ?>>
                        <?php echo esc_html($cat->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="s" placeholder="<?php esc_attr_e('Search stores...', 'mallfinder'); ?>" value="<?php echo esc_attr(isset($_GET['s']) ? $_GET['s'] : ''); ?>">
            
            <button type="submit" class="btn btn-primary"><?php _e('Filter', 'mallfinder'); ?></button>
            <a href="<?php echo get_post_type_archive_link('store'); ?>" class="btn btn-outline"><?php _e('Reset', 'mallfinder'); ?></a>
        </form>
        
        <?php global $wp_query; ?>
        <p style="color: var(--text-secondary); margin-bottom: 1rem;">
            <?php printf(_n('%s store found', '%s stores found', $wp_query->found_posts, 'mallfinder'), number_format_i18n($wp_query->found_posts)); ?>
        </p>
        
        <?php if (have_posts()) : ?>
            <div class="store-list" style="background: var(--surface-color); border-radius: var(--radius-lg); padding: 1rem;">
                <?php while (have_posts()) : the_post(); ?>
                    <?php echo mallfinder_get_store_card(get_the_ID()); ?>
                <?php endwhile; ?>
            </div>
            
            <?php mallfinder_pagination(); ?>
            
        <?php else : ?>
            <div class="text-center" style="padding: 3rem;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; color: var(--text-secondary); margin: 0 auto 1rem;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <h3><?php _e('No stores found', 'mallfinder'); ?></h3>
                <p><?php _e('Try adjusting your search or filter criteria.', 'mallfinder'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>

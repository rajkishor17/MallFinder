<?php
/**
 * Main Template File
 * 
 * @package MallFinder
 */

get_header();
?>

<main class="container" style="padding-top: 2rem; padding-bottom: 2rem;">
    <?php if (have_posts()) : ?>
        <h1 class="page-title">
            <?php
            if (is_home() && !is_front_page()) {
                single_post_title();
            } elseif (is_archive()) {
                the_archive_title();
            } elseif (is_search()) {
                printf(__('Search Results for: %s', 'mallfinder'), '<span>' . get_search_query() . '</span>');
            } else {
                _e('Latest Posts', 'mallfinder');
            }
            ?>
        </h1>
        
        <div class="mall-grid" style="margin-top: 2rem;">
            <?php while (have_posts()) : the_post(); ?>
                <article class="card">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="card-image">
                            <?php the_post_thumbnail('medium_large'); ?>
                        </a>
                    <?php endif; ?>
                    
                    <div class="card-content">
                        <h2 class="card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        
                        <p class="card-meta">
                            <?php echo esc_html(get_the_date()); ?> • <?php the_author(); ?>
                        </p>
                        
                        <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 20)); ?></p>
                        
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                            <?php _e('Read More', 'mallfinder'); ?>
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        
        <div class="pagination">
            <?php
            the_posts_pagination(array(
                'prev_text' => '← ' . __('Previous', 'mallfinder'),
                'next_text' => __('Next', 'mallfinder') . ' →',
            ));
            ?>
        </div>
        
    <?php else : ?>
        <div style="text-align: center; padding: 4rem 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 1rem; display: block; opacity: 0.5;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            <h2><?php _e('No Content Found', 'mallfinder'); ?></h2>
            <p style="color: var(--color-slate-500);"><?php _e('There are no posts to display.', 'mallfinder'); ?></p>
        </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>

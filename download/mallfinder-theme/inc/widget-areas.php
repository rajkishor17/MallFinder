<?php
/**
 * Widget Areas Registration
 *
 * @package MallFinder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Widget Areas
 */
function mallfinder_register_widget_areas() {
    // Sidebar Widget Area
    register_sidebar(array(
        'name'          => __('Mall Sidebar', 'mallfinder'),
        'id'            => 'mall-sidebar',
        'description'   => __('Widgets in this area will appear on mall detail pages.', 'mallfinder'),
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Footer Widget Area 1
    register_sidebar(array(
        'name'          => __('Footer Widget 1', 'mallfinder'),
        'id'            => 'footer-1',
        'description'   => __('First footer widget area.', 'mallfinder'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Footer Widget Area 2
    register_sidebar(array(
        'name'          => __('Footer Widget 2', 'mallfinder'),
        'id'            => 'footer-2',
        'description'   => __('Second footer widget area.', 'mallfinder'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Footer Widget Area 3
    register_sidebar(array(
        'name'          => __('Footer Widget 3', 'mallfinder'),
        'id'            => 'footer-3',
        'description'   => __('Third footer widget area.', 'mallfinder'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Footer Widget Area 4
    register_sidebar(array(
        'name'          => __('Footer Widget 4', 'mallfinder'),
        'id'            => 'footer-4',
        'description'   => __('Fourth footer widget area.', 'mallfinder'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'mallfinder_register_widget_areas');

/**
 * Recent Malls Widget
 */
class Mallfinder_Recent_Malls_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'mallfinder_recent_malls',
            __('MallFinder - Recent Malls', 'mallfinder'),
            array('description' => __('Display recent malls.', 'mallfinder'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $number = intval($instance['number']);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $malls = get_posts(array(
            'post_type'      => 'mall',
            'posts_per_page' => $number,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));
        
        if ($malls) {
            echo '<ul class="recent-malls-widget">';
            foreach ($malls as $mall) {
                echo '<li>';
                echo '<a href="' . get_permalink($mall->ID) . '">' . esc_html($mall->post_title) . '</a>';
                echo '<span class="date">' . get_the_date('M j, Y', $mall->ID) . '</span>';
                echo '</li>';
            }
            echo '</ul>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Recent Malls', 'mallfinder');
        $number = isset($instance['number']) ? absint($instance['number']) : 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mallfinder'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of malls to show:', 'mallfinder'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['number'] = absint($new_instance['number']);
        return $instance;
    }
}

/**
 * Mall Search Widget
 */
class Mallfinder_Mall_Search_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'mallfinder_mall_search',
            __('MallFinder - Mall Search', 'mallfinder'),
            array('description' => __('A search form for malls.', 'mallfinder'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        // Get all states, cities, areas
        $states = get_posts(array('post_type' => 'state', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
        
        echo '<form class="mall-search-form" method="GET" action="' . get_post_type_archive_link('mall') . '">';
        echo '<div class="form-group">';
        echo '<input type="text" name="s" placeholder="' . __('Search malls...', 'mallfinder') . '" class="form-input">';
        echo '</div>';
        
        if ($states) {
            echo '<div class="form-group">';
            echo '<select name="state" id="widget-state-select" class="form-select">';
            echo '<option value="">' . __('All States', 'mallfinder') . '</option>';
            foreach ($states as $state) {
                echo '<option value="' . $state->ID . '">' . esc_html($state->post_title) . '</option>';
            }
            echo '</select>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<select name="city" id="widget-city-select" class="form-select" disabled>';
            echo '<option value="">' . __('All Cities', 'mallfinder') . '</option>';
            echo '</select>';
            echo '</div>';
        }
        
        echo '<button type="submit" class="btn btn-primary" style="width: 100%;">' . __('Search', 'mallfinder') . '</button>';
        echo '</form>';
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Find a Mall', 'mallfinder');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mallfinder'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        return $instance;
    }
}

/**
 * Store Categories Widget
 */
class Mallfinder_Store_Categories_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'mallfinder_store_categories',
            __('MallFinder - Store Categories', 'mallfinder'),
            array('description' => __('Display store categories.', 'mallfinder'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $args['before_widget'];
        
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        $categories = get_terms(array(
            'taxonomy'   => 'store_category',
            'hide_empty' => true,
        ));
        
        if ($categories && !is_wp_error($categories)) {
            echo '<ul class="store-categories-widget">';
            foreach ($categories as $category) {
                echo '<li>';
                echo '<a href="' . get_term_link($category) . '">';
                echo esc_html($category->name);
                echo '<span class="count">(' . $category->count . ')</span>';
                echo '</a>';
                echo '</li>';
            }
            echo '</ul>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Store Categories', 'mallfinder');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mallfinder'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = sanitize_text_field($new_instance['title']);
        return $instance;
    }
}

/**
 * Register Custom Widgets
 */
function mallfinder_register_widgets() {
    register_widget('Mallfinder_Recent_Malls_Widget');
    register_widget('Mallfinder_Mall_Search_Widget');
    register_widget('Mallfinder_Store_Categories_Widget');
}
add_action('widgets_init', 'mallfinder_register_widgets');

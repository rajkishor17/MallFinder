<?php
/**
 * Template Functions
 *
 * @package MallFinder
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display Social Media Links
 */
function mallfinder_social_links($echo = true) {
    $social = mallfinder_get_social_links();
    $html = '<div class="social-links">';
    
    $icons = array(
        'facebook'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
        'twitter'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>',
        'instagram' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
        'linkedin'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>',
        'youtube'   => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
        'whatsapp'  => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>',
    );
    
    foreach ($social as $platform => $url) {
        if ($url) {
            if ($platform === 'whatsapp') {
                $url = 'https://wa.me/' . preg_replace('/[^0-9]/', '', $url);
            }
            $html .= '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($platform) . '">';
            $html .= $icons[$platform];
            $html .= '</a>';
        }
    }
    
    $html .= '</div>';
    
    if ($echo) {
        echo $html;
    }
    
    return $html;
}

/**
 * Display Breadcrumbs
 */
function mallfinder_breadcrumbs() {
    $html = '<nav class="breadcrumb">';
    $html .= '<a href="' . home_url() . '">' . __('Home', 'mallfinder') . '</a>';
    
    if (is_singular('mall')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<a href="' . get_post_type_archive_link('mall') . '">' . __('Malls', 'mallfinder') . '</a>';
        $html .= ' <span class="separator">/</span> ';
        $html .= '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_singular('store')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<a href="' . get_post_type_archive_link('store') . '">' . __('Stores', 'mallfinder') . '</a>';
        $html .= ' <span class="separator">/</span> ';
        $html .= '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_post_type_archive('mall')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<span class="current">' . __('Malls', 'mallfinder') . '</span>';
    } elseif (is_post_type_archive('store')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<span class="current">' . __('Stores', 'mallfinder') . '</span>';
    } elseif (is_singular('state')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<a href="' . get_post_type_archive_link('mall') . '">' . __('Malls', 'mallfinder') . '</a>';
        $html .= ' <span class="separator">/</span> ';
        $html .= '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_singular('city')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<a href="' . get_post_type_archive_link('mall') . '">' . __('Malls', 'mallfinder') . '</a>';
        $html .= ' <span class="separator">/</span> ';
        $state_id = get_post_meta(get_the_ID(), '_city_state', true);
        if ($state_id) {
            $html .= '<a href="' . get_permalink($state_id) . '">' . get_the_title($state_id) . '</a>';
            $html .= ' <span class="separator">/</span> ';
        }
        $html .= '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_singular('area')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<a href="' . get_post_type_archive_link('mall') . '">' . __('Malls', 'mallfinder') . '</a>';
        $html .= ' <span class="separator">/</span> ';
        $city_id = get_post_meta(get_the_ID(), '_area_city', true);
        if ($city_id) {
            $state_id = get_post_meta($city_id, '_city_state', true);
            if ($state_id) {
                $html .= '<a href="' . get_permalink($state_id) . '">' . get_the_title($state_id) . '</a>';
                $html .= ' <span class="separator">/</span> ';
            }
            $html .= '<a href="' . get_permalink($city_id) . '">' . get_the_title($city_id) . '</a>';
            $html .= ' <span class="separator">/</span> ';
        }
        $html .= '<span class="current">' . get_the_title() . '</span>';
    } elseif (is_tax('store_category')) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<a href="' . get_post_type_archive_link('store') . '">' . __('Stores', 'mallfinder') . '</a>';
        $html .= ' <span class="separator">/</span> ';
        $html .= '<span class="current">' . single_term_title('', false) . '</span>';
    } elseif (is_search()) {
        $html .= ' <span class="separator">/</span> ';
        $html .= '<span class="current">' . sprintf(__('Search Results for: %s', 'mallfinder'), get_search_query()) . '</span>';
    }
    
    $html .= '</nav>';
    
    echo $html;
}

/**
 * Get Mall Card HTML
 */
function mallfinder_get_mall_card($mall_id) {
    $image = get_the_post_thumbnail_url($mall_id, 'mall-thumbnail');
    if (!$image) {
        $image = get_template_directory_uri() . '/assets/images/default-mall.jpg';
    }
    
    $address = mallfinder_format_address($mall_id);
    $stores_count = mallfinder_get_stores_count($mall_id);
    
    ob_start();
    ?>
    <article class="card">
        <div class="card-image">
            <a href="<?php the_permalink($mall_id); ?>">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($mall_id)); ?>">
            </a>
            <?php if ($stores_count > 0) : ?>
                <span class="card-badge"><?php echo $stores_count; ?> <?php _e('Stores', 'mallfinder'); ?></span>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <h3 class="card-title">
                <a href="<?php the_permalink($mall_id); ?>"><?php echo esc_html(get_the_title($mall_id)); ?></a>
            </h3>
            <div class="card-meta">
                <span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <?php echo esc_html(wp_trim_words($address, 8)); ?>
                </span>
            </div>
            <p class="card-description">
                <?php echo esc_html(wp_trim_words(get_the_excerpt($mall_id), 15)); ?>
            </p>
            <a href="<?php the_permalink($mall_id); ?>" class="btn btn-outline btn-sm">
                <?php _e('View Details', 'mallfinder'); ?>
            </a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

/**
 * Get Store Card HTML
 */
function mallfinder_get_store_card($store_id) {
    $logo = mallfinder_get_store_logo($store_id);
    $mall_id = get_post_meta($store_id, '_store_mall', true);
    $floor = get_post_meta($store_id, '_store_floor', true);
    $unit = get_post_meta($store_id, '_store_unit', true);
    $categories = mallfinder_get_store_categories($store_id);
    
    ob_start();
    ?>
    <article class="store-item">
        <div class="store-logo">
            <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_the_title($store_id)); ?>">
        </div>
        <div class="store-info">
            <h4 class="store-name">
                <a href="<?php the_permalink($store_id); ?>"><?php echo esc_html(get_the_title($store_id)); ?></a>
            </h4>
            <?php if ($categories) : ?>
                <span class="store-category"><?php echo esc_html(implode(', ', $categories)); ?></span>
            <?php endif; ?>
            <?php if ($floor || $unit) : ?>
                <span class="store-location"><?php echo esc_html(trim($floor . ' - ' . $unit, ' -')); ?></span>
            <?php endif; ?>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

/**
 * Display Pagination
 */
function mallfinder_pagination($query = null) {
    if (!$query) {
        global $wp_query;
        $query = $wp_query;
    }
    
    if ($query->max_num_pages <= 1) {
        return;
    }
    
    $big = 999999999;
    
    $pagination = paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, get_query_var('paged')),
        'total'     => $query->max_num_pages,
        'type'      => 'array',
        'prev_text' => __('&laquo; Previous', 'mallfinder'),
        'next_text' => __('Next &raquo;', 'mallfinder'),
    ));
    
    if ($pagination) {
        echo '<div class="pagination">';
        foreach ($pagination as $page) {
            echo $page;
        }
        echo '</div>';
    }
}

/**
 * Get Malls Count by Location
 */
function mallfinder_get_mall_count_by_location($location_id, $location_type = 'state') {
    $args = array(
        'post_type'      => 'mall',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
    );
    
    $meta_key = '_mall_' . $location_type;
    
    // For cities, also check areas
    if ($location_type === 'city') {
        $areas = get_posts(array(
            'post_type'      => 'area',
            'posts_per_page' => -1,
            'meta_key'       => '_area_city',
            'meta_value'     => $location_id,
            'fields'         => 'ids',
        ));
        
        if ($areas) {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key'     => '_mall_city',
                    'value'   => $location_id,
                    'compare' => '=',
                ),
                array(
                    'key'     => '_mall_area',
                    'value'   => $areas,
                    'compare' => 'IN',
                ),
            );
        } else {
            $args['meta_key'] = '_mall_city';
            $args['meta_value'] = $location_id;
        }
    } else {
        $args['meta_key'] = $meta_key;
        $args['meta_value'] = $location_id;
    }
    
    return count(get_posts($args));
}

/**
 * Render Mall Map
 */
function mallfinder_render_map($args = array()) {
    $defaults = array(
        'id'          => 'mall-map',
        'height'      => '400px',
        'malls'       => array(),
        'show_markers' => true,
        'cluster'     => false,
    );
    
    $args = wp_parse_args($args, $defaults);
    
    $malls_data = array();
    
    if (!empty($args['malls'])) {
        foreach ($args['malls'] as $mall_id) {
            $lat = get_post_meta($mall_id, '_mall_latitude', true);
            $lng = get_post_meta($mall_id, '_mall_longitude', true);
            
            if ($lat && $lng) {
                $malls_data[] = array(
                    'id'        => $mall_id,
                    'title'     => get_the_title($mall_id),
                    'permalink' => get_permalink($mall_id),
                    'lat'       => floatval($lat),
                    'lng'       => floatval($lng),
                    'address'   => mallfinder_format_address($mall_id),
                    'image'     => get_the_post_thumbnail_url($mall_id, 'mall-thumbnail'),
                );
            }
        }
    }
    
    ob_start();
    ?>
    <div class="map-container<?php echo $args['height'] === '500px' ? ' tall' : ''; ?>">
        <div id="<?php echo esc_attr($args['id']); ?>"></div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        if (typeof L !== 'undefined') {
            var map = L.map('<?php echo esc_attr($args['id']); ?>').setView([
                <?php echo esc_js(get_option('mallfinder_default_lat', '40.7128')); ?>,
                <?php echo esc_js(get_option('mallfinder_default_lng', '-74.0060')); ?>
            ], <?php echo esc_js(get_option('mallfinder_default_zoom', '12')); ?>);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            var mallsData = <?php echo json_encode($malls_data); ?>;
            var bounds = [];
            
            mallsData.forEach(function(mall) {
                var marker = L.marker([mall.lat, mall.lng]).addTo(map);
                var popupContent = '<div class="map-popup">';
                popupContent += '<h4><a href="' + mall.permalink + '">' + mall.title + '</a></h4>';
                if (mall.image) {
                    popupContent += '<img src="' + mall.image + '" alt="" style="width:100%;max-width:200px;margin:10px 0;border-radius:4px;">';
                }
                popupContent += '<p>' + mall.address + '</p>';
                popupContent += '<a href="' + mall.permalink + '" class="btn btn-sm btn-primary">View Details</a>';
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
    <?php
    return ob_get_clean();
}

/**
 * Helper: Get All Mall IDs for Map
 */
function mallfinder_get_all_mall_ids_for_map() {
    return get_posts(array(
        'post_type'      => 'mall',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key'     => '_mall_latitude',
                'compare' => 'EXISTS',
            ),
            array(
                'key'     => '_mall_longitude',
                'compare' => 'EXISTS',
            ),
        ),
    ));
}

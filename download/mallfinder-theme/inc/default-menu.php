<?php
/**
 * Default Menu Fallback
 *
 * @package MallFinder
 */

function mallfinder_default_menu() {
    echo '<ul>';
    echo '<li><a href="' . home_url() . '">' . __('Home', 'mallfinder') . '</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('mall') . '">' . __('Malls', 'mallfinder') . '</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('store') . '">' . __('Stores', 'mallfinder') . '</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('state') . '">' . __('States', 'mallfinder') . '</a></li>';
    echo '<li><a href="' . get_post_type_archive_link('city') . '">' . __('Cities', 'mallfinder') . '</a></li>';
    echo '</ul>';
}

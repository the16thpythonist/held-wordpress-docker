<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.06.18
 * Time: 11:57
 */

/**
 * Registers the 'highlight' custom post type with wordpress
 *
 * CHANGELOG
 *
 * Changed 28.06.2018
 * Added the 'menu_icon' field to the post type registration
 */
function hh_register_type_highlight() {
    $args = array(
        'label'         => 'Highlight',
        'description'   => 'Describes a Highlight post',
        'public'        => true,
        'show_ui'       => true,
        'menu_position' => 5,
        'map_meta_cap'  => true,
        'supports'      => array(
            'title',
            'editor',
            'excerpt'
        ),
        'menu_icon'     => 'dashicons-star-filled'
    );
    register_post_type('highlight', $args);
}
add_action('init', 'hh_register_type_highlight');


function hh_register_highlight_taxonomy_category() {
    register_taxonomy(
        'highlight_category',
        'highlight',
        array(
            'public' => true,
            'label' => 'Category'
        )
    );
}
add_action('init', 'hh_register_highlight_taxonomy_category');
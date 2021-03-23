<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 14.06.18
 * Time: 16:02
 */

use the16thpythonist\Wordpress\Functions\PostUtil;
use the16thpythonist\Wordpress\Scopus\PublicationPostModification;

//$m = new PublicationPostModification();
//$m->register();

/**
 * CHANGELOG
 *
 * Added 03.05.2018
 *
 * Changed 04.05.2018
 * I realized there was no reason to create an entirely new post type from scratch as the built in type "post" would be
 * unused although it already offers everything needed for a publication, wo I chose to instead of creating a new type
 * with a action hook, this function now changes the labels on the built in "post" to display "Publication" instead.
 *
 * Changed 14.06.2018
 * Moved the function from the main plugin file to 'cpt/post.php'
 *
 * Changed 28.06.2018
 * Added the 'menu_icon' field to the post type registration
 */
function hh_register_type_publication($args, $post_type) {
    if ( $post_type == 'post' ){
        $args['label'] = 'Publication';
        $args['menu_icon'] = 'dashicons-format-aside';
    }
    return $args;
}


/***
 * CHANGELOG
 *
 * Added 03.05.2018
 *
 * Changed 14.06.2018
 * Moved the function from the main plugin file to 'cpt/post.php' and renamed it from 'hh_register_taxonomy_journal'
 * to 'hh_post_register_taxonomy_journal' to make it clear for which post type the function is
 */
function hh_post_register_taxonomy_journal() {
    register_taxonomy(
        'journal',
        'post',
        array(
            'label' => 'Journal',
            'public' => true,
        )
    );
}


/**
 * CHANGELOG
 *
 * Added 03.05.2018
 *
 * Changed 14.06.2018
 * Moved the function from the main plugin file to 'cpt/post.php' and renamed the function from
 * 'hh_register_taxonomy_collaboration' to 'hh_post_register_taxonomy_collaboration' to make clear for which post type
 * the taxonomy is being added.
 */
function hh_post_register_taxonomy_collaboration() {
    register_taxonomy(
        'collaboration',
        'post',
        array(
            'label' => 'Collaboration',
            'public' => true
        )
    );
}


/**
 * CHANGELOG
 *
 * Added 03.05.2018
 *
 * Changed 14.06.2018
 * Moved the function from the main plugin file to 'cpt/post.php' and changed the name from
 * 'hh_register_taxonomy_author' to 'hh_post_register_taxonomy_author' to make clear for which post type the
 * taxonomy is being added
 */
function hh_post_register_taxonomy_author() {
    register_taxonomy(
        'author',
        'post',
        array(
            'label' => 'Author',
            'public' => true
        )
    );
}


function hh_post_register_taxonomy_category() {
    register_taxonomy(
        'category',
        'post',
        array(
            'label' => 'Category',
            'public' => true,
        )
    );
}


/**
 * This function will be used as the callback for a init action hook and adds a new taxonomy to the publication 'post'
 * type. The taxonomy 'selection' will be used to add tags to publications and based on what kind of tag, the tagged
 * publications can be linked to on static pages using a special shortcode.
 *
 * CHANGELOG
 *
 * Added 14.06.2018
 */
function hh_post_register_taxonomy_selection() {
    register_taxonomy(
        'selection',
        'post',
        array(
            'label' => 'Selection',
            'public' => true
        )
    );
}


/**
 * CHANGELOG
 *
 * Changed 14.06.2018
 * Moved the function from the main plugin file to 'cpt/post.php' and renamed the function from
 * 'hh_register_taxonomy_category' to 'hh_post_modify_taxonomy_category' to emphasize, that there is no new tax added
 * here, but instead one is being modified
 *
 * @param $args
 * @param $taxonomy
 * @param $object_type
 * @return mixed
 */
function hh_post_modify_taxonomy_category( $args, $taxonomy, $object_type ) {
    if ( $taxonomy == 'category' ) {
        $args['capabilities'] = array(
            'manage_terms'=> 'read',
            'edit_terms'=> 'read',
            'delete_terms'=> 'read',
            'assign_terms' => 'read'
        );
    }
    return $args;
}


/**
 * This function is a action executed, when a wordpress post is being saved. It only for the 'post' post type.
 * Whenever a publication is being saved it changes the date of publication of the wordpress post object to the
 * publication date of the publication it describes, by reading the data from the 'published' meta field of the post.
 *
 * CHANGELOG
 *
 * Added 14.06.2018
 *
 * @param $post_id
 * @return mixed
 */
function hh_post_save_post($post_id) {
    if (!PostUtil::isSavingPostType('post', $post_id)) {
        return $post_id;
    }

    $publishing_date_key = 'published';
    $publishing_date_exists = metadata_exists('post', $post_id, $publishing_date_key);
    if ($publishing_date_exists) {
        $publishing_date = get_post_meta($post_id, $publishing_date_key, true);

        global $wpdb;
        $where = array('ID' => $post_id);
        $what = array('post_date' => $publishing_date);
        $wpdb->update($wpdb->posts, $what, $where);
    }
}


/**
 * CHANGELOG
 *
 * Added 14.06.2018
 *
 * Changed 14.06.2018
 * Added the action hook to add the 'selection'taxonomy
 *
 */

// Registering the post type
//add_filter('register_post_type_args', 'hh_register_type_publication', 20, 2);
// Registering all the new taxonomies for the post type
//add_action('init', 'hh_post_register_taxonomy_author');
//add_action('init', 'hh_post_register_taxonomy_collaboration');
//add_action('init', 'hh_post_register_taxonomy_journal');
//add_action('init', 'hh_post_register_taxonomy_selection');
//add_action('init', 'hh_post_register_taxonomy_category');

// Modifying the category taxonomy for the standard post type
//add_filter( 'register_taxonomy_args', 'hh_post_modify_taxonomy_category', 10, 3);

// The action hook for when a post is being saved. Modifies the publish date of the post to match the publication
//add_filter('save_post', 'hh_post_save_post', 10, 1);

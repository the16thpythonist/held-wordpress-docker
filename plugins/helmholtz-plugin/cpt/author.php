<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.06.18
 * Time: 18:25
 */

/**
 * registers the 'author' custom post type with wordpress
 *
 * CHANGELOG
 *
 * Changed 28.06.2018
 * Added the field 'menu_icon' to the args of the post type registration to give it a more descriptive icon
 *
 */
function hh_register_type_author() {
    $args = array(
        'label'         => 'Author',
        'description'   => 'Describes all the authors, that are being used to fetch publications from',
        'public'        => true,
        'show_ui'       => true,
        'menu_position' => 5,
        'map_meta_cap'  => true,
        'supports'      => array(),
        'menu_icon'     => 'dashicons-businessman'
    );
    register_post_type('author', $args);
}
add_action('init', 'hh_register_type_author');

/**
 * CHANGELOG
 *
 * Added 23.04.2018
 * A function, that saves a author CPT once the publish button is pressed. This is required to correctly save the
 * custom meta box contents for the scopus meta box into the according meta fields of the post model
 *
 * Changed 28.06.2018
 * Since there now is a new input widget for the author affiliation whitelist and blacklist, these have to be saved
 * differently as well: The checkbox paremeters of the POST array are now checked and based on their name, which
 * contains the affiliation id to blacklist/whitelist, theses ids are added into an array and saved as csv into their
 * corresponding meta fields
 *
 * @param $post_id
 * @return mixed
 */
function hh_author_save_post($post_id) {
    if ('author' !== $_POST['post_type']) {
        return $post_id;
    }

    $keys = array('first_name', 'last_name', 'scopus_author_id', 'categories');
    foreach ($keys as $key) {
        update_post_meta($post_id, $key, $_POST[$key]);
    }

    // Getting all the whitelisted
    $whitelist = array();
    $blacklist = array();
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'whitelist') !== false) {
            $affiliation_id = explode('-', $key)[1];
            $whitelist[] = $affiliation_id;
        }
        if (strpos($key, 'blacklist') !== false) {
            $affiliation_id = explode('-', $key)[1];
            $blacklist[] = $affiliation_id;
        }
    }
    update_post_meta($post_id, 'scopus_whitelist', implode(',', $whitelist));
    update_post_meta($post_id, 'scopus_blacklist', implode(',', $blacklist));

    // Changing the title to be the first and last name of the author described
    if (metadata_exists('post', $post_id,'first_name') && metadata_exists('post', $post_id, 'last_name')){
        global $wpdb;
        $first_name = get_post_meta($post_id, 'first_name', true);
        $last_name = get_post_meta($post_id, 'last_name', true);
        // Creating the title from the first name and last name
        $title = $last_name . ', ' . $first_name;
        // Updating the title in the wordpress database only for the post with the given id
        $where = array('ID' => $post_id);
        $wpdb->update($wpdb->posts, array('post_title' => $title), $where);
    }

    return $post_id;
}
add_action( 'save_post', 'hh_author_save_post' );

function hh_add_author_meta_boxes() {
    add_meta_box(
        'author-scopus-meta',
        'Scopus Meta',
        'hh_author_scopus_meta_box',
        'author',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'hh_add_author_meta_boxes');


function hh_author_scopus_meta_box($post) {
    require_once HELMHOLTZ_PLUGIN_PATH . '/pages/author_scopus_meta_box.php';
}
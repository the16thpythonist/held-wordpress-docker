<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 14.06.18
 * Time: 18:13
 */


function hh_search_title_where($where, $wp_query) {
    global $wpdb;
    $title = $wp_query->get('hh_title');
    if ($title) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like($title)) . '%\'';
    }
    return $where;
}
add_filter('posts_where', 'hh_search_title_where', 10, 2);


/**
 * Called after hitting the 'start' button in the indico section of the helmholtz options page. Starts the indico event
 * fetch process as a wordpress background task.
 *
 * @return string
 */
function hh_ajax_start_indico() {
    global $hh_request_events;
    $hh_request_events->dispatch();
    echo 'dispatched indico background process';
    return '';
}
add_action('wp_ajax_start_indico', 'hh_ajax_start_indico');


/**
 * Called every few milliseconds, after the scopus session has been started from the scopus section of the helmholtz
 * options page. This function simply gets the contents of the file 'scopus_session.json', which stores the info about
 * the ongoing scopus fetch session in JSON format and send them back as a response.
 *
 * @return string
 */
function hh_ajax_update_scopus_session() {
    $json = file_get_contents(plugin_dir_path(__FILE__) . 'scopus_session.json');
    echo $json;
    return '';
}
add_action('wp_ajax_update_scopus_session', 'hh_ajax_update_scopus_session');


/**
 * Called every few milliseconds, after the indico session has been started from the indico session of the helmholtz
 * options page. This function simply gets the contents of the file 'indico_session.json', which stores the info about
 * the ongoing indico fetch session in JSON format and send them back as a response to be displayed to the user.
 *
 * @return string
 */
function hh_ajax_update_indico_session() {
    $json = file_get_contents(plugin_dir_path(__FILE__) . 'indico_session.json');
    echo $json;
    return '';
}
add_action('wp_ajax_update_indico_session', 'hh_ajax_update_indico_session');


/**
 * Called after hitting the start button on the scopus section of the helmholtz options page.
 * Will dispatch the 'scopus-fetch' background process.
 *
 * @return string
 */
function hh_ajax_start_scopus() {
    global $hh_scopus_fetch_request;
    $hh_scopus_fetch_request->dispatch();
    $json = file_get_contents(plugin_dir_path(__FILE__) . 'scopus_session.json');
    echo $json;
    return '';
}
add_action('wp_ajax_start_scopus', 'hh_ajax_start_scopus');

/**
 * Called every few milliseconds by the 'selection-search' widget to search all the publication posts by the title
 * entered into the input field at that time. This function will return the 6 top results of the title search in a
 * JSON object, where the keys are the post ids of the posts and the values are the titles.
 *
 * @return string
 */
function hh_ajax_selection_search() {
    $title = $_GET['search'];
    $wp_query = new WP_Query(
        array(
            'post_type' => 'post',
            'posts_per_page' => '6',
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
            'hh_title' => $title
        )
    );
    $posts = $wp_query->get_posts();
    $return = array();
    foreach ($posts as $post) {
        /* @var $post WP_Post */
        $post_id = $post->ID;
        $post_title = $post->post_title;
        $return[$post_id] = $post_title;
    }
    echo json_encode($return);
    return '';
}
add_action('wp_ajax_selection_search', 'hh_ajax_selection_search');


/**
 * Ajax method for the action 'selection_update'
 *
 * Called, when pressing the send button on the 'selection-search' widget. This function will receive the post id of
 * the selected post and the selection term name as parameters through the GET request and will then add the given
 * selection term to the post with the given post id. A message will be returned, that will be displayed in the widget
 * in success of the action
 *
 * CHANGELOG
 *
 * Added 14.06.2018
 *
 * Changed 17.06.2018
 *
 * Previously this ajax call function echoed only the post id and the selection term name concat. as one string. This
 * has been extended to a fully descriptive message string.
 *
 * Now the post object is being loaded from the post id, so that the post title can be extracted. The title is then
 * used to formulate a more descriptive return message for the user
 */
function hh_ajax_selection_update() {
    $post_id = $_GET['post'];
    $post_selection = $_GET['selection'];

    // Getting the post object from the post id, to get the post title, which will be used to create a better
    // return message, with which the user can actually see if the action was successful or not
    /* @var $post WP_Post */
    $post = get_post($post_id);
    $post_title = $post->post_title;

    // Adding the taxonomy term to the post
    $result = wp_set_object_terms($post_id, $post_selection, 'selection', true);
    echo 'The post "' . $post_title . '" has been marked with the selection "' . $post_selection . '"';
}
add_action('wp_ajax_selection_update', 'hh_ajax_selection_update');


/**
 * Ajax method for the action 'author_affiliations'. Returns affiliation info data structure based on passed scopus id
 *
 * CHANGELOG
 *
 * Added 28.06.2018
 *
 * @since 0.0.1.9
 *
 * @throws InvalidArgumentException if the passed author id does not belong to any author post
 *
 * @return array
 */
function hh_ajax_author_affiliations() {
    $post_id = $_GET['post'];
    $author_id = $_GET['author'];

    $author = hh_get_author($author_id);
    // Getting the affiliations
    $affiliations = $author->fetchAffiliations();
    // creating the array to return with the whitelist and blacklist values for the affiliations
    $result = array();
    foreach ($affiliations as $key => $name) {
        $content = array('name' => $name);
        $content['whitelist'] = (in_array($key, $author->scopus_whitelist) ? true : false);
        $content['blacklist'] = (in_array($key, $author->scopus_blacklist) ? true : false);

        $result[$key] = $content;
    }
    echo json_encode($result);
}
add_action('wp_ajax_author_affiliations', 'hh_ajax_author_affiliations');


/**
 * Loads a JSON file from the designated json asset folder, if its name has been passed as parameter
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 *
 * @since 0.0.1.13
 */
function hh_ajax_get_json() {
    $name = $_GET['file'];
    $data = hh_load_json($name);
    //echo $data;
    echo json_encode($data);
    }
add_action('wp_ajax_get_json', 'hh_ajax_get_json');
add_action('wp_ajax_nopriv_get_json', 'hh_ajax_get_json');
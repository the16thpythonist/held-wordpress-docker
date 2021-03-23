<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 11.06.18
 * Time: 17:49
 *
 * CHANGELOG
 *
 * Added 12.06.2018
 */


/**
 * Shortcode 'contact'
 *
 * This shortcode will create nested div sections for displaying the contact data of a scientist in charge of a certain
 * research page.
 * This contact data can include the name, the institute of affiliation, the email and the role of the scientist for
 * the specific project.
 *
 * Parameters:
 * email        : The mail address of the person
 * name         : The full name of the person
 * institute    : The institute affiliated with
 * role         : The role for the project
 *
 * CHANGELOG
 *
 * Changed 17.06.2018
 *
 * Changed the name of the function from 'hh_contact_shortcode' to 'hh_shortcode_contact' so that it fits the pattern
 * of the other functions.
 *
 * The template was being included using the TEMPLATE_DIR constant and the shortcodes sub-path within the following
 * string. Replaced that with usage of the more specific constant for the shorcodes path directly.
 *
 * @param $atts
 * @return string
 */
function hh_shortcode_contact($atts) {
    // In case the post type is a page
    $post_type = get_post_type();

    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/contact.php';
    return ob_get_clean();
}


/**
 * Shortcode 'events'
 *
 * Create a list (ul) of bullet points, each containing the starting date of the events, followed by the name, which
 * is also a link to the specific page of the event-post. This shortcode creates the HTML elements with specific classes
 * to be used in a sidebar widget area.
 *
 * Parameters
 * max  : The amount of events to be displayed
 *
 * CHANGELOG
 *
 * Changed 17.06.2018
 *
 * The template was being included using the TEMPLATE_DIR constant and the shortcodes sub-path within the following
 * string. Replaced that with usage of the more specific constant for the shorcodes path directly.
 * @param $atts
 * @return string
 */
function hh_shortcode_events($atts) {
    $default = array(
        'max' => 5,
    );

    $max = $default['max'];

    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/events.php';
    return ob_get_clean();
}


/**
 * Shortcode 'list-events'
 *
 * Display a list of events in an unsorted list (ul), where each bullet point contains the name of the event, which is
 * also a link to the specific page, and a short excerpt of when it starts and the description.
 *
 * Parameters:
 * max  : The integer amount of bullet points to display
 *
 * @param array $atts: The array of attributes passed on from the shortcode engine
 * @return string
 */
function hh_shortcode_list_events($atts) {
    $max = 5;
    if (is_array($atts) && array_key_exists('max', $atts)) {
        $max = $atts['max'];
    }

    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/list_events.php' ;
    return ob_get_clean();
}


/**
 * Shortcode 'display-recent-publications'
 *
 * Display a unsorted list (ul), where each bullet point contains the name of a publication and the link to the specific
 * post page, followed by the authors of the publication, the journal and the publishing year. The publications
 * displayed in the list are those, that have been published the most recent.
 *
 * Parameters:
 * max          : The integer amount of how many bullet points to display
 * category     : The category name
 * selection    : The selection term name
 *
 * @param array $atts: The array of parameters passed on from the shortcode engine
 * @return string
 */
function hh_shortcode_display_recent_publications($atts) {
    $default = array(
        'max' => 5,
        'category' => '',
        'selection' => '',
    );
    $atts = array_replace($default, $atts);

    $max = $atts['max'];
    $category = $atts['category'];
    $selection = $atts['selection'];

    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/display_recent_publications.php';
    return ob_get_clean();
}


/**
 * Shortcode 'display-recent-theses'
 *
 * Display a unsorted list (ul), where each bullet point contains the name of a phd thesis, which links to the actual
 * thesis-post page, followed by the institute and university, which hosted the work and the name of the student, that
 * wrote the thesis.
 *
 * Parameters:
 * max  : The integer amount of how many bullet  points to be displayed
 *
 * @param $atts
 * @return string
 */
function hh_shortcode_display_recent_theses($atts) {
    $default = array(
        'max' => 5
    );
    $atts = array_replace($default, $atts);

    $max = $atts['max'];

    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/display_recent_theses.php';
    return ob_get_clean();
}

/**
 * This shortcode will display a list (ul) of links to publication posts of these publications, that have been tagged
 * with the according 'selection' tax. term
 *
 * CHANGELOG
 *
 * Added 17.06.2018
 *
 * @param array $atts: The parameters passed through the shortcode
 * @return string
 */
function hh_shortcode_display_selected_publications($atts) {
    $default = array(
        'selection' => '',
        'max' => 5
    );
    $atts = array_replace($default, $atts);
    $max = $atts['max'];
    $selection = $atts['selection'];
    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/display_selected_publications.php';
    return ob_get_clean();
}

/**
 * Shortcode 'display-recent-highlights'
 *
 * Displays a list of the most recent 'highlight'-type posts in the format of nested div containers. Each begins with
 * the title of the highlight post, which is also a link to the actual post page and also has a little excerpt of
 * the highlight description.
 *
 * Parameters:
 * category     : The string slug of the category, by which the highlights are supposed to be filtered. If this is given
 *                only the highlights tagged with the given term will be displayed
 * max          : The int amount of how many entries the list is supposed to have
 *
 * @param array $args: The parameters passed to the shortcode
 * @return string: The HTML content string
 */
function hh_shortcode_display_recent_highlights($args) {
    $default = array(
        'category' => '',
        'max' => 5
    );
    $args = array_replace($default, $args);
    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/display_recent_highlights.php';
    return ob_get_clean();
}

/**
 * Shortcode 'display-categories'
 *
 * This shortcode will display a listing (ul) of all the non empty categories for the publications, where each entry
 * also contains the exact amount of publications for that category in brackets after the name. Also the name acts as
 * a link to the category overview page of all the posts
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 *
 * @since 0.0.1.12
 *
 * @param $args
 * @return string
 */
function hh_shortcode_list_categories($args) {
    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/list_categories.php';
    return ob_get_clean();
}


/**
 * Shortcode 'display-collaborations'
 *
 * This shortcode will display a listing (ul) of non empty collaboration terms. Each item consists of the name of the
 * collaboration with the exact amount of posts for that collab in brackets after the name. The names also act as links
 * to the category overview page for the collaboration with all the belonging posts.
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 *
 * @since 0.0.1.12
 *
 * @param $args
 * @return string
 */
function hh_shortcode_list_collaborations($args) {
    ob_start();
    include HELMHOLTZ_SHORTCODE_TEMPLATE_PATH . '/list_collaborations.php';
    return ob_get_clean();
}


add_shortcode('contact', 'hh_shortcode_contact');
// add_shortcode('events', 'hh_shortcode_events');
// Adding the short code for creating a list of the most recent events
// foradd_shortcode('list-events', 'hh_shortcode_list_events');
// Adding the short code for creating a listing of the most recent publications
//add_shortcode('display-recent-publications', 'hh_shortcode_display_recent_publications');
// Adding the short code for displaying a listing of the most recent PhD theses
add_shortcode('display-recent-theses', 'hh_shortcode_display_recent_theses');

add_shortcode('display-selected-publications', 'hh_shortcode_display_selected_publications');

add_shortcode('display-recent-highlights', 'hh_shortcode_display_recent_highlights');

add_shortcode('list-categories', 'hh_shortcode_list_categories');

add_shortcode('list-collaborations', 'hh_shortcode_list_collaborations');
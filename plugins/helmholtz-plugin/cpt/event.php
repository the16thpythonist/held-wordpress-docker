<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.06.18
 * Time: 18:16
 */


use Log\LogPost;
use the16thpythonist\Wordpress\Functions\PostUtil;


class Event {

    public $id;
    public $title;
    public $description;
    public $indico_id;
    public $start;
    public $published;
    public $url;
    public $creator;
    public $location;
    public $type;

    /**
     * CHANGELOG
     *
     * Changed 12.06.2018
     *
     * Event constructor.
     * @param null $post_id
     */
    public function __construct($post_id=NULL)
    {
        $post = get_post($post_id);

        $this->id = $post->ID;
        $this->title = $post->post_title;
        $this->description = $post->post_content;
        $this->published = $post->post_date;

        $this->start = get_post_meta($post_id, 'start_date', true);
        $this->url = get_post_meta($post_id, 'url', true);
        $this->indico_id = get_post_meta($post_id, 'indico_id', true);

        $this->location = $this->loadLocation();
        $this->creator = $this->loadCreator();
        $this->type = $this->loadType();
    }

    /**
     * Returns a string of the starting time of the event in a specific date format.
     *
     * CHANGELOG
     *
     * Added 12.06.2018
     *
     * @param string $date_format
     * @return false|string
     */
    public function getStartingDate($date_format='Y-m-d') {
        $starting_time = strtotime($this->start);
        return date($date_format, $starting_time);
    }

    private function loadCreator() {
        return $this->loadSingleTermName('creator');
    }

    private function loadType() {
        return $this->loadSingleTermName('type');
    }

    private function loadLocation() {
        return $this->loadSingleTermName('location');
    }

    /**
     * CHANGELOG
     *
     * Changed 12.06.2018
     * Added an additional if clause, that checks if the return of the wp_get_post_terms function is actually an array
     * and only then returns the name of the first item of that array, because if there were no terms to get, it
     * resulted in an error
     *
     * @param $key string: The type of taxenomy, of which to get the terms from
     * @return string: The 'name' attribute of the first term
     */
    private function loadSingleTermName($key) {
        /* @var $first_term WP_Term */
        $terms = wp_get_post_terms($this->id, $key);
        if (is_array($terms) && count($terms) > 0) {
            return $terms[0]->name;
        } else {
            return '';
        }
    }
}



/**
 * registers the 'event' custom post type with wordpress
 *
 * At the beginning of a wordpress instance this function adds registers the 'event' custom post type, which will be
 * used for Indico events
 *
 * CHANGELOG
 *
 * Changed 28.06.2018
 * Added the 'menu_icon' field to the post type registration
 */
function hh_register_type_event() {
    $args = array(
        'label'                 => 'Event',
        'description'           => 'Describes a indico upcoming event',
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'menu_position'         => 5,
        'map_meta_cap'          => true,
        'supports'              => array('title', 'editor', 'custom-fields'),
        'menu_icon'             => 'dashicons-groups'
    );
    register_post_type('event', $args);
}
//add_action('init', 'hh_register_type_event');


/**
 * Registers the 'type' taxonomy with the event post type. The type taxonomy will contain the type of event, that is
 * being described, examples would be 'conference' and 'meeting'
 */
function hh_register_event_taxonomy_type() {
    register_taxonomy(
        'type',
        'event',
        array(
            'public' => true,
            'label' => 'Type'
        )
    );
}
//add_action('init', 'hh_register_event_taxonomy_type');


/**
 * Registers the 'creator' taxonomy with the event post type. The creator taxonomy contains the name of the creator of
 * the event.
 */
function hh_register_event_taxonomy_creator() {
    register_taxonomy(
        'creator',
        'event',
        array(
            'public' => true,
            'label' => 'Creator'
        )
    );
}
//add_action('init', 'hh_register_event_taxonomy_creator');


function hh_register_event_taxonomy_location() {
    register_taxonomy(
        'location',
        'event',
        array(
            'public' => true,
            'label' => 'Location',
        )
    );
}
//add_action('init', 'hh_register_event_taxonomy_location');


/**
 * Action hook for saving a event post.
 *
 * If there is a specific event id specified in the single request meta box, then this event will be requested from the
 * indico api and the event will be used to create a new event post from the post, that is being saved.
 * Caution: This may well override an existing post!
 *
 * CHANGELOG
 *
 * Added 16.07.2018
 *
 * Changed 27.10.2018
 * Now using the utility function isSavingPostType to determine whether or not a "event" post is being saved upon
 * calling the post hook or not.
 *
 * Changed 30.10.2018
 * With using the new utility function there was a complication with the function also getting executed, when a post
 * was saved with the funci
 *
 * @since 0.0.1.11
 *
 * @param string $post_id the id of the post that is being saved
 * @return string
 */
function hh_event_save_post(string $post_id) {
    if (!PostUtil::isSavingPostType('event', $post_id)){
        return $post_id;
    }


    //Checking if an event id has been entered. If that is the case, that means, that the user wants to
    //fill this post with the data from the event site that has been specified. Which means the event is
    //being modified.

    // 30.10.2018
    // Since with using the new utility function for checking if the correct post type is saved, this function also gets
    // called, when a post is saved with "wp_insert_post", we need to check if the POST array even contains any key.
    if (array_key_exists('event_id', $_POST) && $_POST['event-id'] !== '') {
        $url = $_POST['indico-url'];
        $id = $_POST['event-id'];
        $event = hh_indico_single_event($url, $id);

        hh_event_post($post_id, $event);

        return $post_id;
    }
    return $post_id;
}
//add_action('save_post', 'hh_event_save_post');


/**
 * creates a new post from the event, given the post id and the Event object to be based upon
 *
 * CHANGELOG
 *
 * Added 16.07.2018
 *
 * @since 0.0.1.11
 *
 * @param string $post_id                           the wordpress post id of the post, that is to be replaced with the
 *                                                  event post.
 * @param \the16thpythonist\Indico\Event $event     the Event object, on which to base to post data
 */
function hh_event_post(string $post_id, \the16thpythonist\Indico\Event $event) {
    update_post_meta($post_id, 'indico_id', $event->getID());
    update_post_meta($post_id, 'start_date', $event->getStartTime()->format(HELMHOLTZ_DATETIME_FORMAT));
    update_post_meta($post_id, 'url', $event->getURL());

    wp_set_object_terms($post_id, array($event->getCreator()->getFullName()), 'creator', false);
    wp_set_object_terms($post_id, array($event->getType()), 'type', false);
    wp_set_object_terms($post_id, array($event->getLocation()), 'location', false);

    // Changing the title and the description
    global $wpdb;
    $title = $event->getTitle();
    $description = $event->getDescription();
    $where = array('ID' => $post_id);
    $args = array(
        'post_title' => $title,
        'post_content' => $description,
    );
    $wpdb->update($wpdb->posts, $args, $where);
}


/**
 * Adds all the meta boxes to the event post type
 *
 * CHANGELOG
 *
 * Added 16.07.2018
 *
 * @since 0.0.1.11
 */
function hh_add_event_meta_boxes() {
    add_meta_box(
        'event-request-meta',
        'Request Event from Indico',
        'hh_event_request_meta_box',
        'event',
        'normal',
        'high'
    );
}
//add_action('add_meta_boxes', 'hh_add_event_meta_boxes');


/**
 * Echos the actual html code of the meta box, that is used to request a single event from indico
 *
 * CHANGELOG
 *
 * Added 16.07.2018
 *
 * @since 0.0.1.11
 */
function hh_event_request_meta_box() {
    require_once HELMHOLTZ_PAGES_PATH . '/event_request_meta_box.php';
}
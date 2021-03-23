<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 11.06.18
 * Time: 15:04
 */

use the16thpythonist\Wordpress\Functions\PostUtil;

class Thesis
{
    public $id;
    public $published;
    public $title;
    public $abstract;
    public $university;
    public $institute;
    public $author_name;
    public $first_assessor;
    public $second_assessor;
    public $url;

    private $published_year;

    private $first_name;
    private $last_name;

    public function __construct($post_id)
    {
        $post = get_post($post_id);

        $this->id = $post_id;

        $this->title = $post->post_title;
        $this->abstract = $post->post_content;
        $this->published = $post->post_date;

        // TODO: This can defintely be done in a loop
        $this->university = get_post_meta($post_id, 'university', true);
        $this->institute = get_post_meta($post_id, 'institute', true);
        $this->first_name = get_post_meta($post_id, 'first_name', true);
        $this->last_name = get_post_meta($post_id, 'last_name', true);
        $this->first_assessor = get_post_meta($post_id, 'first_assessor', true);
        $this->second_assessor = get_post_meta($post_id, 'second_assessor', true);
        $this->url = get_post_meta($post_id, 'url', true);
    }

    public function getPublishingYear() {
        return date('Y', strtotime($this->published));
    }

    public function getAuthorFullName() {
        return $this->last_name . ', ' . $this->first_name;
    }

    /**
     * CHANGELOG
     *
     * Added 12.06.2018
     */
    public function getAuthorIndexedName() {
        return $this->first_name[0] . '. ' . $this->last_name;
    }

}


class ThesisPostRegistration {
    public $post_type;

    const LABEL = 'Thesis';
    const DESCRIPTION = 'Describes a PhD thesis';
    const ICON = 'dashicons-welcome-learn-more';

    /**
     * ThesisPostRegistration constructor.
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     *
     * @param string $post_type
     */
    public function __construct(string $post_type)
    {
        $this->post_type = $post_type;
    }

    /**
     * Returns the string, that was used to describe the post type
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     *
     * @return string
     */
    public function getPostType() {
        return $this->post_type;
    }

    // *****************************
    // REGISTRATION OF THE POST TYPE
    // *****************************

    /**
     * This function does all the registration for the post type
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     */
    public function register() {
        // Registering the post type in general
        add_action('init', array($this, 'registerPostType'));

        // Registering the appropriate actions for the saving process
        add_action('save_post', array($this, 'savePost'), 10, 1);

        // Registering the metaboxes
        add_action('add_meta_boxes', array($this, 'registerMetaboxes'));

        // This function performs all the hook registrations for modifying the list view of the posts of this post
        // type in the admin view
        $this->registerAdminListViewModification();
    }

    /**
     * Calls the according wordpress function, which registers the new post type in general with the correct arguments
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     */
    public function registerPostType() {
        if (!post_type_exists($this->post_type)) {
            $args = array(
                'label'                 => self::LABEL,
                'description'           => self::DESCRIPTION,
                'public'                => true,
                'publicly_queryable'    => true,
                'show_ui'               => true,
                'menu_position'         => 5,
                'map_meta_cap'          => true,
                'menu_icon'             => self::ICON
            );
            register_post_type($this->post_type, $args);
        }
    }

    /**
     * Wraps the callback registrations for all the hooks, that are involved in modifying the list view of this post
     * type  within the admin area of the site
     *
     * CHANGELOG
     *
     * Added 17.12.2019
     */
    public function registerAdminListViewModification(){

        // This filter will be used to define, which columns the list view is supposed to have
        add_filter(
            $this->insertPostType('manage_%s_posts_columns'),
            array($this, 'manageColumns'),
            10, 1
        );

        // This action will be used to generate the actual contents for the columns
        add_action(
            $this->insertPostType('manage_%s_posts_custom_column'),
            array($this, 'contentColumns'),
            10, 2
        );

    }

    /**
     * This function takes a string, which has to contain exactly one string position for inserting a
     * string with the "sprintf" function.
     * This position will be inserted with the post type string of this class.
     * This function will be needed in situations, where the name of a hook is dynamically dependant on the post type
     * for example.
     *
     * EXAMPLE
     *
     * $this->post_type = "author"
     * $this->insertPostType("manage_%s_posts")
     * >> "manage_author_posts"
     *
     * CHANGELOG
     *
     * Added 10.12.2019
     *
     * @param string $template
     * @return string
     */
    public function insertPostType(string $template) {
        return sprintf($template, $this->post_type);
    }

    // *********************************
    // SAVING PROCESS FOR THIS POST TYPE
    // *********************************

    /**
     * This function handles the saving process for a post. This function gets hooked into a wordpress hook, which
     * fires, when a post is being saved.
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     *
     * @param string $post_id
     * @return string
     */
    public function savePost(string $post_id) {
        // If the saving process, which triggered this callback to be executed is for a post, which is not actually of
        // this post type, we do nothing here
        if (!PostUtil::isSavingPostType($this->post_type, $post_id)) {
            return $post_id;
        }

        $keys = array(
            'first_name',
            'last_name',
            'university',
            'institute',
            'published',
            'first_assessor',
            'second_assessor',
            'url',
            'date'
        );
        // Updating the meta values
        foreach ($keys as $key) {
            update_post_meta($post_id, $key, $_POST[$key]);
        }
        return $post_id;
    }

    // ****************************
    // METABOXES FOR THIS POST TYPE
    // ****************************

    /**
     * This function registers the metaboxes for this post type.
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     */
    public function registerMetaboxes() {
        add_meta_box(
            'thesis-meta-information',
            'Thesis details',
            array($this, 'inputMetabox'),
            $this->post_type,
            'normal',
            'high'
        );
    }

    /**
     * This function echos the HTML code, which is required to display the meta box, which is used to input all the
     * additional parameters for the PhD thesis.
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     */
    public function inputMetabox($post) {
        // The PHP / HTML Code needed for creating the whole meta box is way too much, thus it has been outsourced to a
        // separate template file
        require_once HELMHOLTZ_PAGES_PATH . '/thesis_meta_box.php';
    }

    // *****************************
    // MODIFYING THE ADMIN LIST VIEW
    // *****************************

    /**
     * This function will modify the columns array, to register the custom types of columns, which will be displayed in
     * the admin list view for this post type.
     *
     * CHANGELOG
     *
     * Added 17.12.2019
     *
     * @param array $columns
     * @return array
     */
    public function manageColumns(array $columns) {
        $columns = array(
            'cb'                => $columns['cb'],
            'title'             => $columns['title'],
            'thesis_author'     => __('Author'),
            'department'        => __('Department'),
            'date'              => $columns['date']
        );
        return $columns;
    }

    /**
     * If given the name of a column within the admin list view for this post type and the post id for the post in this
     * row, this function will echo the according html content for this very field.
     *
     * CHANGELOG
     *
     * Added 17.12.2019
     *
     * @param $column
     * @param $post_id
     */
    public function contentColumns($column, $post_id) {
        $thesis_post = New ThesisPost($post_id);

        if ($column === 'thesis_author') {
            echo $thesis_post->getAuthorName();
        }

        if ($column === 'department') {
            echo $thesis_post->department;
        }
    }
}


/**
 * Class ThesisPost
 *
 * CHANGELOG
 *
 * Added 16.12.2019
 */
class ThesisPost {
    public static $POST_TYPE;
    public static $REGISTRATION;

    public $ID;
    public $post;
    public $published;

    public $title;
    public $abstract;
    public $university;
    public $department;
    public $first_assessor;
    public $second_assessor;
    public $url;
    public $date;

    private $published_year;

    public $first_name;
    public $last_name;

    public static $PROPERTY_META_MAP = array(
        'university'        => 'university',
        'department'        => 'institute',
        'first_name'        => 'first_name',
        'last_name'         => 'last_name',
        'first_assessor'    => 'first_assessor',
        'second_assessor'   => 'second_assessor',
        'url'               => 'url',
        'date'              => 'date'
    );

    /**
     * ThesisPost constructor.
     *
     * CHANGELOG
     *
     * Added 16.12.2019
     *
     * @param int $post_id
     */
    public function __construct(int $post_id) {
        $this->ID = $post_id;
        $this->post = get_post($post_id);

        // The title of the PhD thesis is mapped as the title of the post
        $this->title = $this->post->post_title;
        // The content of the post is the abstract of the thesis
        $this->abstract = $this->post->post_content;

        // All the other fields of ThesisPost are mapped as meta properties of the post
        // This loop will dynamically assign the properties of the object with the loaded meta values of the post.
        foreach (self::$PROPERTY_META_MAP as $property => $name) {
            $this->{$property} = PostUtil::loadSinglePostMeta($post_id, $name, '');
        }
    }

    /**
     * Returns a string, which contains the full name of the author.
     * The name will be in the format "[LAST NAME], [FIRST NAME]"
     *
     * CHANGELOG
     *
     * Added 17.12.2019
     *
     * @return string
     */
    public function getAuthorName() {
        return sprintf(
            '%s, %s',
            $this->first_name,
            $this->last_name
        );
    }

}


/**
 * This function registers the new custom post type 'thesis', which will be used to display a PhD Thesis
 *
 * CHANGELOG
 *
 * Added 11.06.2018
 *
 * Changed 11.06.2018
 * Set the argument 'publicly queryable' to true, as that is needed to call a post representation on the site front end
 *
 * Changed 28.06.2018
 * Added the 'menu_icon' field to the post type registration.
 *
 * Deprecated 16.12.2019
 *
 * @deprecated
 */
function hh_register_type_thesis() {
    $args = array(
        'label'                 => 'PhD Thesis',
        'description'           => 'Custom type to describe a thesis',
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'menu_position'         => 5,
        'map_meta_cap'          => true,
        'supports'              => array(),
        'menu_icon'             => 'dashicons-welcome-learn-more'
    );
    register_post_type('thesis', $args);
}


/**
 * This function calls the wordpress function to register a new meta box in the 'thesis' post type editor. Uses the
 * function 'hh_thesis_meta_box' as callback to output the actual HTML code
 *
 * CHANGELOG
 *
 * Added 11.06.2018
 *
 * Deprecated 16.12.2019
 *
 * @deprecated
 */
function hh_add_thesis_meta_boxes() {
    add_meta_box(
        'thesis-meta-information',
        'Thesis details',
        'hh_thesis_meta_box',
        'thesis',
        'normal',
        'high'
    );
}


/**
 * The callback function for the thesis meta box. It is called when a new thesis post is being created or an existing
 * one is modified. This function outputs all the actual HTML of the meta box.
 * The actual template code for the meta box is located in /pages/thesis_meta_box.php
 *
 * CHANGELOG
 *
 * Added 11.06.2018
 *
 * Deprecated 16.12.2019
 *
 * @deprecated
 *
 * @param $post WP_Post: The wordpress post object of the post, for which the meta box is being displayed
 */
function hh_thesis_meta_box($post) {
    // The PHP / HTML Code needed for creating the whole meta box is way too much, thus it has been outsourced to a
    // separate template file
    require_once HELMHOLTZ_PAGES_PATH . '/thesis_meta_box.php';
}


/**
 *
 * CHANGELOG
 *
 * Added 11.06.2018
 *
 * Changed 20.10.2018
 * Changed the if statement, that checks for the post type to use a function, which also checks if the saving process
 * is actually happening over the wordpress backend, as there was an error caused when the 'wp_insert_post' function
 * was called.
 *
 * Deprecated 16.12.2019
 *
 * @deprecated
 *
 * @param $post_id
 * @return mixed
 */
function hh_thesis_save_post($post_id) {
    if (!PostUtil::isSavingPostType('thesis', $post_id)) {
        return $post_id;
    }

    $keys = array(
        'first_name',
        'last_name',
        'university',
        'institute',
        'published',
        'first_assessor',
        'second_assessor',
        'url'
    );
    // Updating the meta values
    foreach ($keys as $key) {
        update_post_meta($post_id, $key, $_POST[$key]);
    }

    return $post_id;
}



// Registering the thesis type with wordpress
//add_action('init', 'hh_register_type_thesis');
// Adding the meta box to the 'add new post' site in the admin dashboard
//add_action('add_meta_boxes', 'hh_add_thesis_meta_boxes');
// Registering the function, which will save the values from the thesis meta box to post meta data, once the post
// is being saved
//add_action('save_post', 'hh_thesis_save_post', 10, 1);

$post_registration = new ThesisPostRegistration('thesis');
$post_registration->register();
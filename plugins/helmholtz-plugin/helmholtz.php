<?php
/**
 * Plugin Name: Helmholtz MTDTS
 * Plugin URI: https://ufo.kit.edu/held
 * Description: The Plugin containing the custom functionality for the MTDTS website
 * Author: Jonas Teufel
 * Version: 0.0.2
 * Author URI: google.com
 * License: GPLv2 or later
 */

require_once 'vendor/autoload.php';

use Scopus\ScopusApi;
use Indico\IndicoApi;
use Log\LogPost;
use the16thpythonist\KITOpen\KITOpenApi;
use the16thpythonist\Wordpress\CommandMenu;
use the16thpythonist\Wordpress\Data\DataPost;
use the16thpythonist\Wordpress\Scopus\AuthorPost;
use the16thpythonist\Wordpress\Scopus\WpScopus;
use the16thpythonist\Wordpress\Indico\WpIndico;

define('HELMHOLTZ_PLUGIN_PATH', dirname(__FILE__));
require_once HELMHOLTZ_PLUGIN_PATH . '/defines.php';

/**
 * CHANGELOG
 *
 * Changed 17.07.2018
 * Added the Command menu page to be registered with wordpress as well
 *
 * Changed 28.09.2018
 * Added 'wp-data-safe' module, which enables saving generic data inside of wordpress posts and not files.
 * The DataPost system is used by calling the "register" method on the DataPost fassade.
 *
 * Changed 19.11.2018
 * Removed all register calls except the WpScopus facade, as it now registers all the required packages internally
 *
 * Changed 07.01.2019
 * Now using a separate package 'wp-indico' for indico functionality, which is registered here.
 */
// "WpScopus" is the facade object, which is used to access all of the Scopus publication plugin functionality
// With the register method all important post types are registered, as well as styles and scripts.
WpScopus::register(array(), API_KEY);

// 07.01.2019
// Using the wp-indico software package to enable indico functionality
$indico_sites = array(
    array(
        'name'          => 'desy',
        'key'           => '829e3826-39ad-4be3-b50f-1d25397e67bd',
        'url'           => 'https://indico.desy.de/indico',
        'categories'    => array(
            '388',
            '385'
        )
    )
);
WpIndico::register('event', $indico_sites);


/**
 * CHANGELOG
 *
 * Changed 17.07.2018 - 0.0.1.12
 * Added the 'commands.php' file to be imported as well. The file contains all the "Command" sub classes, which are
 * being used to define some sort of background task/computation.
 *
 * Changed 19.07.2018 - 0.0.1.13
 * Added the 'defines.php' file to be imported as well. This file contains all the plugin specific constant definitions.
 * Those have been moved from this file to the new file.
 *
 * Changed 14.08.2018
 * Added 'widgets.php' file to be imported as well. This file contains all the functions necessary to create the custom
 * widgets to be used on the site.
 */
require_once HELMHOLTZ_PLUGIN_PATH . '/defines.php';
// Importing the shortcodes
require_once HELMHOLTZ_PLUGIN_PATH . '/shortcodes.php';
// Importing the utility functions
require_once HELMHOLTZ_PLUGIN_PATH . '/functions.php';
// Importing all the ajax function registers
require_once HELMHOLTZ_PLUGIN_PATH . '/ajax.php';
// Importing all the commands
require_once HELMHOLTZ_PLUGIN_PATH . '/commands.php';
// Importing all the widget defines
require_once HELMHOLTZ_PLUGIN_PATH . '/widgets.php';


/**
 * CHANGELOG
 *
 * Changed 28.06.2018
 * Renamed from 'hh_enqueue_admin_options' to 'hh_enqueue_admin_scripts'.
 * Added the script files 'admin.js' and 'functions.js' to be enqueued as well
 */
function hh_enqueue_admin_scripts() {

    wp_enqueue_script('helmholtz-functions', plugin_dir_url(__FILE__) . 'js/functions.js');

    wp_enqueue_script('helmholtz-author-type', plugin_dir_url(__FILE__) . 'js/admin.js');

    wp_enqueue_script('scopus-options', plugin_dir_url(__FILE__) . 'js/options.js');

    wp_enqueue_script('jquery-ui-hh', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'));
}
add_action('admin_enqueue_scripts', 'hh_enqueue_admin_scripts');

function hh_enqueue_admin_styles() {
    wp_enqueue_style(
        'scopus-options-style',
        plugin_dir_url(__FILE__) . 'css/options.css'
    );
}
add_action('init', 'hh_enqueue_admin_styles');


/**
 * Enqueues JS Scripts to be loaded with the wordpress site conditionally
 *
 * Enqueues special scripts for special sites only:
 *
 * For the 'Author Metrics' site:
 * - The D3.js framework for data visualisation
 * - The script containing the actual code to generate the author force layout
 * - The general helper functions script 'functions.js'
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 *
 * @since 0.0.1.13
 */
function hh_enqueue_scripts() {
    if (is_page(array('Author Metrics')) && false) {
        wp_enqueue_script('d3-main', 'https://d3js.org/d3.v2.js');
        wp_enqueue_script('force-layout', plugin_dir_url(__FILE__) . 'js/test.js');
        wp_enqueue_script('functions', plugin_dir_url(__FILE__) . 'js/functions.js');
        //wp_enqueue_script('d3-geom', 'http://github.com/d3/d3.geom.js');
    }
    wp_enqueue_script('jquery-ui-hh', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'));
}
add_action('wp_enqueue_scripts', 'hh_enqueue_scripts');

define('COLLABORATIONS', array('CMS', 'AUGER', 'ATLAS'));


/**
 * CHANGELOG
 *
 * Changed 12.06.2018
 * Added the thesis post type. All the required functions being located in a separate file, the file is being
 * loaded here.
 *
 * Changed 14.06.2018
 * Removed all the functions, that were necessary for modifying the 'post' post type to properly represent a publication
 * data model. Moved them to their own file in 'cpt/post.php'. Loading that file here
 */

// 'THESIS' POST TYPE
require_once HELMHOLTZ_CUSTOM_POST_TYPES_PATH . '/thesis.php';
// 'POST' POST TYPE (Modified to be represent publications)
require_once HELMHOLTZ_CUSTOM_POST_TYPES_PATH . '/post.php';
// 'EVENT' POST TYPE
require_once HELMHOLTZ_CUSTOM_POST_TYPES_PATH . '/event.php';
// 'AUTHOR' POST TYPE
//require_once HELMHOLTZ_CUSTOM_POST_TYPES_PATH . '/author.php';
// 'HIGHLIGHT' POST TYPE
require_once HELMHOLTZ_CUSTOM_POST_TYPES_PATH . '/highlight.php';


/**
 * CHANGELOG
 *
 * Added 24.05.2018
 * The function which adds the author CPT to wordpress
 *
 * Changed 14.06.2018
 * Moved the function from the main plugin file to 'cpt/post.php'
 */
// removed

/**
 * CHANGELOG
 *
 * Added 03.05.2018
 * The two callback function for creating the category and tag taxonomy for the custom "publication" type
 *
 * Removed 04.05.2018
 * With the transition from using the custom "publication" type to just changing the built in "post" type, adding the
 * category and tag tax. is obsolete because they are already part of the "post" type
 */
// removed

/**
 * CHANGELOG
 *
 * Added 03.05.2018
 * The action hooks for registering the new 'publication' post type and the belonging taxonomies
 *
 * Changed 04.05.2018
 * I realized that the default post type "post" was absolutely obsolete after introducing the publication post type and
 * that I essentially just copied the "post" type with my new custom type so I just changed a few labels on the "post"
 * type and now essentially this built in type is being used for publications.
 * The changed are being made with a filter for the arguments of a new post type the moment it is being registered
 *
 * Removed 14.06.2018
 * The action hooks are no longer executed here, as that has been moved to its own file for each custom post type, with
 * the only thing happening here is the import of those files.
 */





function pprint($expression) {
    print("<pre>".print_r($expression,true)."</pre>");
}


function hh_get_authors() {
    $api = new ScopusApi(API_KEY);
    $author_posts = get_posts(
        array(
            'numberposts' => -1,
            'post_type' => 'author'
        )
    );
    $authors = array();
    foreach ($author_posts as $post) {
        $post_id = $post->ID;
        $author = new Author($api, $post_id);
        $authors[] = $author;
    }
    return $authors;
}

/**
 * A function that fetches events from indico and posts those, that have not already been posted
 *
 * CHANGELOG
 *
 * Added 31.05.2018
 * This function will get all the indico events from the specified indico web pages
 *
 * Removed 08.08.2018
 * Moved the whole functionality into a separate "Command" class in the "commands.php" module
 */


function hh_scopus_menu() {
    add_options_page('Scopus', 'Scopus', 'manage_options', 'hh-scopus-menu', 'hh_scopus_options');
}


function hh_scopus_options() {

    include_once plugin_dir_path(__FILE__) . 'pages/scopus_options.php';

}


add_action('admin_menu', 'hh_scopus_menu');



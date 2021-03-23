<?php

define( 'HELMHOLTZ_THEME_VERSION', '0.0.6.12');

define( 'TEMPLATE_DIR', get_stylesheet_directory() . '/templates/');

define( 'HELMHOLTZ_PLUGIN_FOLDER_PATH', dirname(dirname(dirname(__FILE__))) . '/plugins/helmholtz');
define( 'HELMHOLTZ_PLUGIN_FILE_PATH', HELMHOLTZ_PLUGIN_FOLDER_PATH . '/helmholtz.php');

define( 'HELMHOLTZ_THEME_FOLDER_PATH', dirname(__FILE__));

use Log\LogPost;


/**
 * Checks if the helmholtz plugin has been installed.
 *
 * CHANGELOG
 *
 * Added 11.06.2018
 *
 * Changed 11.06.2018
 * Before the paths for the helmholtz plugin folder and main file were defined as constants within the function, but
 * because those paths might get relevant for other functions as well, they have been made global constants
 */
function hh_plugin_available() {
    // First checks if the plugin is actually installed
    $plugin_exists = is_dir(HELMHOLTZ_PLUGIN_FOLDER_PATH) && file_exists(HELMHOLTZ_PLUGIN_FILE_PATH);
    if ($plugin_exists) {
        return true;
    }
    return false;
}

/**
 * This function will do everything, that is
 *
 * CHANGELOG
 *
 * Added 11.06.2018
 *
 * Changed 11.06.2018
 * Before the
 */
function hh_backwards_compatibility() {
    if (!hh_plugin_available()) {
        // Only starting the rename action if the file actually exists
        $post_template_path = HELMHOLTZ_THEME_FOLDER_PATH . '/single-post.php';
        $disabled_post_template_path = HELMHOLTZ_THEME_FOLDER_PATH . '/__single-post.php';

        if (file_exists($post_template_path)) {
            rename($post_template_path, $disabled_post_template_path);
        }
    }
}
hh_backwards_compatibility();

/**
 * Function to be hooked into the wordpress init, used to include all necessary css stylesheets into the html header
 * for the actual content page (does not ddo it for the admin page).
 * Now one could wonder: Why would you include ANY css files additionally, when there is the style.css that is non
 * voluntary and which gets included automatically? The answer is the version! When including stylesheets this way a
 * version (in this case the global theme version can be specified) with the style.css the wordpress version will be
 * used. A updating version number will force the client browser to download a new stylesheet with every theme version
 * and prevents from using an outdated cached version.
 *
 * CHANGELOG
 *
 * Added 02.05.2018
 */
function hh_enqueue_theme_styles() {
    if (!is_admin()) {
        // This will essentially be the main style.css sheet just in another folder, so that it can be manually enqueued
        // like this and the version ca be set
        //wp_enqueue_style('child-style', get_stylesheet_directory_uri() . 'style.css');
        wp_enqueue_style(
                'main-style',
                get_stylesheet_directory_uri() . '/css/style.css',
                array('child-style', 'bootstrap'),
                HELMHOLTZ_THEME_VERSION
        );
    }
}

/**
 * Function being hooked into the wordpress init, used to include all necessary javascript files in the html header for
 * the actual content page (does not do it for the admin page).
 *
 * CHANGELOG
 *
 * Added 02.05.2018
 */
function hh_enqueue_theme_scripts() {
    if (!is_admin()) {
        // The script which contains the functionality for the header animation and the dynamic resizing
        wp_enqueue_script(
                'main-style',
                get_stylesheet_directory_uri() . '/js/header.js',
                NULL,
                HELMHOLTZ_THEME_VERSION
        );
    }
}

add_action('wp_head', 'hh_enqueue_theme_styles', 1);
add_action('init', 'hh_enqueue_theme_scripts', 20);

//require_once get_stylesheet_directory() . 'class-tgm-plugin-activation.php';



// CHANGELOG

// Added 23.04.2018
// Added a php library to the project which enables to specify the plugins needed by the theme in exactly this function
// upon starting the theme there will be a notice about the requirements and a button to install them from wordpress

// Removed 10.06.2018
// This has been disabled due to debugging a problem with the rpc api of wordpress and python interaction.
// The feature isnt needed anyways, since migration will always be done by github cloning the wordpress folder.
//add_action( 'tgmpa_register', 'main_register_required_plugins' );

function main_register_required_plugins() {
    $plugins = array(
        array(
            'name'      => 'Meta Box',
            'slug'      => 'meta-box',
            'required'  => true,
        ),
        array(
            'name'      => 'Advanced Custom Fields',
            'slug'      => 'advanced-custom-fields',
            'required'  => true,
        ),
        array(
            'name'      => 'Custom Post Type UI',
            'slug'      => 'custom-post-type-ui',
            'required'  => true,
        )
    );

    $config = array(
        'id'           => 'main',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'parent_slug'  => 'themes.php',            // Parent menu slug.
        'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );

    tgmpa( $plugins, $config );
}


function helmholtz_widgets_init() {
    register_sidebar( array(
        'name' => 'Header Area',
        'id' => 'header_1',
        'before_widget' => '<div>',
        'after_widget' => '</div>'
    ));

    register_widget( 'WP_Widget_Recent_Citations' );
}


/**
 * CHANGELOG
 *
 * Changed 17.06.2018
 *
 * Removed the function which created the contact meta box in the edit post sections, as there is a shortcode for that
 * now.
 * Also removed the function, that created the metabox for creating a thesis post from a static research page, as that
 * is being handled by the plugin.
 */

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles',99);
function child_enqueue_styles() {
    $parent_style = 'parent-style';
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ) );
}
if ( get_stylesheet() !== get_template() ) {
    add_filter( 'pre_update_option_theme_mods_' . get_stylesheet(), function ( $value, $old_value ) {
        update_option( 'theme_mods_' . get_template(), $value );
        return $old_value; // prevent update to child theme mods
    }, 10, 2 );
    add_filter( 'pre_option_theme_mods_' . get_stylesheet(), function ( $default ) {
        return get_option( 'theme_mods_' . get_template(), $default );
    } );
}


/**
 * ADDING THE WIDGETS
 * With this line the function, that registers the widgets in the wordpress site is added to
 * all the other functions adding widgets, via a hook
 */
add_action( 'widgets_init', 'helmholtz_widgets_init' );


/**
 * Core class used to implement a Recent Comments widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class WP_Widget_Recent_Citations extends WP_Widget {

    /**
     * Sets up a new Recent Comments widget instance.
     *
     * @since 2.8.0
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'widget_recent_citations',
            'description' => __( 'Your site&#8217;s most recent comments.' ),
            'customize_selective_refresh' => true,
        );
        parent::__construct( 'recent-citations', __( 'Recent Citations' ), $widget_ops );
        $this->alt_option_name = 'widget_recent_comments';

        if ( is_active_widget( false, false, $this->id_base ) || is_customize_preview() ) {
            add_action( 'wp_head', array( $this, 'recent_comments_style' ) );
        }
    }

    /**
     * Outputs the default styles for the Recent Comments widget.
     *
     * @since 2.8.0
     */
    public function recent_comments_style() {
        /**
         * Filters the Recent Comments default widget styles.
         *
         * @since 3.1.0
         *
         * @param bool   $active  Whether the widget is active. Default true.
         * @param string $id_base The widget ID.
         */
        if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876
            || ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) )
            return;
        ?>
        <style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
        <?php
    }

    /**
     * Outputs the content for the current Recent Comments widget instance.
     *
     * @since 2.8.0
     *
     * @param array $args     Display arguments including 'before_title', 'after_title',
     *                        'before_widget', and 'after_widget'.
     * @param array $instance Settings for the current Recent Comments widget instance.
     */
    public function widget( $args, $instance ) {
        if ( ! isset( $args['widget_id'] ) )
            $args['widget_id'] = $this->id;

        $output = '';

        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Citations' );

        /** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
        $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

        $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
        if ( ! $number )
            $number = 5;

        /**
         * Filters the arguments for the Recent Comments widget.
         *
         * @since 3.4.0
         * @since 4.9.0 Added the `$instance` parameter.
         *
         * @see WP_Comment_Query::query() for information on accepted arguments.
         *
         * @param array $comment_args An array of arguments used to retrieve the recent comments.
         * @param array $instance     Array of settings for the current widget.
         */
        $comments = get_comments( apply_filters( 'widget_comments_args', array(
            'number'      => $number,
            'status'      => 'approve',
            'post_status' => 'publish'
        ), $instance ) );

        $output .= $args['before_widget'];
        if ( $title ) {
            $output .= $args['before_title'] . $title . $args['after_title'];
        }

        $output .= '<ul id="recentcomments">';
        if ( is_array( $comments ) && $comments ) {
            // Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
            $post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
            _prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

            foreach ( (array) $comments as $comment ) {
                $output .= '<li class="recentcomments">';
                /* translators: comments widget: 1: comment author, 2: post link */
                $output .= sprintf( _x( '%2$s', 'widgets' ),
                    '<span class="comment-author-link">' . get_comment_author_link( $comment ) . '</span>',
                    '<a href="' . esc_url( get_comment_link( $comment ) ) . '">' . get_the_title( $comment->comment_post_ID ) . '</a>'
                );
                $output .= '</li>';
            }
        }
        $output .= '</ul>';
        $output .= $args['after_widget'];

        echo $output;
    }

    /**
     * Handles updating settings for the current Recent Comments widget instance.
     *
     * @since 2.8.0
     *
     * @param array $new_instance New settings for this instance as input by the user via
     *                            WP_Widget::form().
     * @param array $old_instance Old settings for this instance.
     * @return array Updated settings to save.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['number'] = absint( $new_instance['number'] );
        return $instance;
    }

    /**
     * Outputs the settings form for the Recent Comments widget.
     *
     * @since 2.8.0
     *
     * @param array $instance Current settings.
     */
    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
        ?>
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

        <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:' ); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>
        <?php
    }

    /**
     * Flushes the Recent Comments widget cache.
     *
     * @since 2.8.0
     *
     * @deprecated 4.4.0 Fragment caching was removed in favor of split queries.
     */
    public function flush_widget_cache() {
        _deprecated_function( __METHOD__, '4.4.0' );
    }
} ?>

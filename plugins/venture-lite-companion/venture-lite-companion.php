<?php
/*
Plugin Name: Venture Lite Companion
Plugin URI: http://www.nimbusthemes.com/free/venture/#plugin
Description: Add many additional features and settings to the Venture Lite theme.
Version: 1.0.0
Author: Nimbus Themes
Author URI: http://www.nimbusthemes.com/
Text Domain: venture-lite-companion
Domain Path: /languages
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// load text domain

add_action( 'plugins_loaded', 'venture_lite_companion_load_textdomain' );
function venture_lite_companion_load_textdomain() {
	load_plugin_textdomain( 'venture-lite-companion', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

$venture_lite_companion_check_theme = wp_get_theme();
if ( ('Venture Lite' != $venture_lite_companion_check_theme->name) && ('Venture Lite' != $venture_lite_companion_check_theme->parent_theme) ) {
	
	add_action( 'admin_notices', 'venture_lite_companion_no_theme' );
	function venture_lite_companion_no_theme() {
	    ?>
	    <div class="notice notice-error is-dismissible">
	        <p><?php _e( 'The Venture Lite Companion plugin provides additional features to the Venture Lite WordPress theme. You currently do not have the Venture Lite theme installed and so will not benifit from this plugin. Please install and activate Venture Lite or deactivate this plugin. ', 'venture-lite-companion' ); ?></p>
	    </div>
	    <?php
	}
	
} else {
	
	add_action( 'plugins_loaded', 'venture_lite_companion' );
	function venture_lite_companion() {
		// Include Kirki
		include_once( get_template_directory() . '/inc/kirki/kirki.php' );
		// include widgets and options
		include_once( dirname( __FILE__ ) . '/inc/options.php' );
		include_once( dirname( __FILE__ ) . '/inc/widgets.php' );
	}
	
}
<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.07.18
 * Time: 19:28
 *
 * CHANGELOG
 *
 * Added 19.07.2018 - 0.0.1.13
 *
 * Changed 08.08.2018 - 0.0.1.14
 * Added the HELMHOLTZ_DATE_FORMAT, which leaves out the time
 *
 * Changed 14.08.2018
 * Added a "HELMHOLTZ_PLUGIN_WIDGET_TEMPLATES_PATH" of the path, that contains the PHP/HTML templates of the
 * custom widgets.
 */

define('API_KEY', '73ff3b99e960a45bb45b08b057c3fba8');

define('HELMHOLTZ_DATETIME_FORMAT', 'Y-m-d H:i:s');
define('HELMHOLTZ_DATE_FORMAT', 'Y-m-d');

// #############
// #   PATHS   #
// #############

define('HELMHOLTZ_CUSTOM_POST_TYPES_PATH', HELMHOLTZ_PLUGIN_PATH . '/cpt');
define('HELMHOLTZ_PAGES_PATH', HELMHOLTZ_PLUGIN_PATH . '/pages');

define('HELMHOLTZ_PLUGIN_TEMPLATES_PATH', HELMHOLTZ_PLUGIN_PATH . '/templates');

define('HELMHOLTZ_PLUGIN_WIDGET_TEMPLATES_PATH', HELMHOLTZ_PLUGIN_TEMPLATES_PATH . '/widgets');

define( 'HELMHOLTZ_SHORTCODE_TEMPLATE_PATH', HELMHOLTZ_PLUGIN_TEMPLATES_PATH . '/shortcodes');


// The path to the assets (includes JSON stored data and images etc)
define( 'HELMHOLTZ_PLUGIN_ASSET_PATH', HELMHOLTZ_PLUGIN_PATH . '/assets');
// The path used to store all the JSON assets
define( 'HELMHOLTZ_PLUGIN_JSON_PATH', HELMHOLTZ_PLUGIN_ASSET_PATH . '/json');

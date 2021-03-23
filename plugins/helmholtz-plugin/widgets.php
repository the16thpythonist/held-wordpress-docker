<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 14.08.18
 * Time: 13:19
 */


// THE ADMIN DASHBOARD TUTORIAL WIDGET

/**
 * Registers the dashboard widget that contains the tutorial in wordpress
 *
 * CHANGELOG
 *
 * Added 14.08.2018
 *
 * @since 0.0.1.15
 */
function hh_widget_dashboard_tutorial() {
    wp_add_dashboard_widget(
        'hh_widget_dashboard_tutorial',
        'Helmholtz Website Overview',
        'hh_widget_dashboard_tutorial_cb'
    );
}
//add_action('wp_dashboard_setup', 'hh_widget_dashboard_tutorial');

/**
 * Echos the actual html code for the widget
 *
 * The function obviously doesnt contain the whole html code. That is part of a template file.
 * But in this function the template file is being included and thus the html is being echoed inside
 * this function
 *
 * CHANGELOG
 *
 * Added 14.08.2018
 *
 * @since 0.0.1.15
 */
function hh_widget_dashboard_tutorial_cb() {
    //ob_start();
    //include_once HELMHOLTZ_PLUGIN_WIDGET_TEMPLATES_PATH . '/dashboard_tutorial.php';
    //return ob_get_clean();
    include_once  HELMHOLTZ_PLUGIN_WIDGET_TEMPLATES_PATH . '/dashboard_tutorial.php';
}
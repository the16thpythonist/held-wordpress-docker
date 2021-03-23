<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



// #################################################
// Extend Kirki options
// #################################################

Kirki::add_section( 'fp-featured', array(
    'title'          => __( 'Frontpage Featured Section', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-featured-toggle',
	'label'       => __( 'Frontpage Featured Status', 'venture-lite-companion' ),
	'section'     => 'fp-featured',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2' => esc_attr__( 'Demo', 'venture-lite-companion' ),
		'3'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-featured-title',
	'label'    => __( 'Featured - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-featured',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the featured section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-featured-sub-title',
	'label'    => __( 'Featured - Sub Title', 'venture-lite-companion' ),
	'section'  => 'fp-featured',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the smaller text in the featured section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'custom',
	'settings'    => 'featured-widget-note',
	'label'       => 'Populate Featured Content',
	'section'     => 'fp-featured',
	'default'     => __( 'To populate the featured content section, you will need to add featured content widgets to the Frontpage Featured widget area. Go to the Widgets section under Apperance in the left sidebar.', 'venture-lite-companion' ),
	'priority'    => 10,
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-featured-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-featured',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'featured',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );

Kirki::add_section( 'fp-action1', array(
    'title'          => __( 'Frontpage Action Row #1', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-action1-toggle',
	'label'       => __( 'Frontpage Action Row Status', 'venture-lite-companion' ),
	'section'     => 'fp-action1',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2' => esc_attr__( 'Demo', 'venture-lite-companion' ),
		'3'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action1-title',
	'label'    => __( 'Action Row #1 - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-action1',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the Action Row #1 section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action1-sub-title',
	'label'    => __( 'Action Row #1 - Sub Title', 'venture-lite-companion' ),
	'section'  => 'fp-action1',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the smaller text in the Action Row #1 section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action1-button-text',
	'label'    => __( 'Action Row #1 - Button Text', 'venture-lite-companion' ),
	'section'  => 'fp-action1',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the text in the button. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action1-button-url',
	'label'    => __( 'Action Row #1 - Button URL', 'venture-lite-companion' ),
	'section'  => 'fp-action1',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is link destination for the button. Leave blank to hide.', 'venture-lite-companion' ),
	'sanitize_callback' => 'venture_lite_companion_sanitize_url'
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action1-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-action1',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'action1',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );





Kirki::add_section( 'fp-about', array(
    'title'          => __( 'Frontpage About Section', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-about-toggle',
	'label'       => __( 'About Status', 'venture-lite-companion' ),
	'section'     => 'fp-about',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2' => esc_attr__( 'Demo', 'venture-lite-companion' ),
		'3'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-about-title',
	'label'    => __( 'About - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-about',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the about section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-about-sub-title',
	'label'    => __( 'About - Sub Title', 'venture-lite-companion' ),
	'section'  => 'fp-about',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the smaller text in the about section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-about-description',
	'label'    => __( 'About - Description', 'venture-lite-companion' ),
	'section'  => 'fp-about',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the smallest text in the about section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'custom',
	'settings'    => 'about-widget-note',
	'label'       => 'Populate About Content',
	'section'     => 'fp-about',
	'default'     => __( 'To populate the About content section, you will need to add About content widgets to the Frontpage About widget areas. Go to the Widgets section under Apperance in the left sidebar.', 'venture-lite-companion' ),
	'priority'    => 10,
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-about-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-about',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'about',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );



Kirki::add_section( 'fp-action2', array(
    'title'          => __( 'Frontpage Action Row #2', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-action2-toggle',
	'label'       => __( 'Frontpage Action Row #2 Status', 'venture-lite-companion' ),
	'section'     => 'fp-action2',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2' => esc_attr__( 'Demo', 'venture-lite-companion' ),
		'3'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action2-title',
	'label'    => __( 'Action Row #2 - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-action2',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the Action Row #2 section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action2-button-text',
	'label'    => __( 'Action Row #2 - Button Text', 'venture-lite-companion' ),
	'section'  => 'fp-action2',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the text in the button. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action2-button-url',
	'label'    => __( 'Action Row #2 - Button URL', 'venture-lite-companion' ),
	'section'  => 'fp-action2',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is link destination for the button. Leave blank to hide.', 'venture-lite-companion' ),
	'sanitize_callback' => 'venture_lite_companion_sanitize_url'
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-action2-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-action2',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'action2',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );





Kirki::add_section( 'fp-team', array(
    'title'          => __( 'Frontpage Team Section', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-team-toggle',
	'label'       => __( 'Team Status', 'venture-lite-companion' ),
	'section'     => 'fp-team',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2' => esc_attr__( 'Demo', 'venture-lite-companion' ),
		'3'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-team-title',
	'label'    => __( 'Team - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-team',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the team section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-team-sub-title',
	'label'    => __( 'Team - Sub Title', 'venture-lite-companion' ),
	'section'  => 'fp-team',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the smaller text in the team section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'custom',
	'settings'    => 'team-widget-note',
	'label'       => 'Populate Team Content',
	'section'     => 'fp-team',
	'default'     => __( 'To populate the Team content section, you will need to add About content widgets to the Frontpage Team widget areas. Go to the Widgets section under Apperance in the left sidebar.', 'venture-lite-companion' ),
	'priority'    => 10,
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-team-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-team',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'team',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );




Kirki::add_section( 'fp-social', array(
    'title'          => __( 'Frontpage Social Media Section', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-social-toggle',
	'label'       => __( 'Social Status', 'venture-lite-companion' ),
	'section'     => 'fp-social',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2' => esc_attr__( 'Demo', 'venture-lite-companion' ),
		'3'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-social-title',
	'label'    => __( 'Social - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-social',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the social section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-social-sub-title',
	'label'    => __( 'Social - Sub Title', 'venture-lite-companion' ),
	'section'  => 'fp-social',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the smaller text in the social section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'custom',
	'settings'    => 'social-widget-note',
	'label'       => __( 'Populate Social Meida Section Content', 'venture-lite-companion' ),
	'section'     => 'fp-social',
	'default'     => __( 'To populate the Social Media section, you will need to add Social Meida widgets to the Social Media widget areas.  Go to the Widgets section under Apperance in the left sidebar.', 'venture-lite-companion' ),
	'priority'    => 10,
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-social-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-social',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'social',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );


Kirki::add_section( 'fp-test', array(
    'title'          => __( 'Frontpage Testimonial Section', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-test-toggle',
	'label'       => __( 'Testimonial Status', 'venture-lite-companion' ),
	'section'     => 'fp-test',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2' => esc_attr__( 'Demo', 'venture-lite-companion' ),
		'3'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'image',
	'settings'    => 'fp-test-image',
	'label'       => __( 'Testimonial Section Image', 'venture-lite-companion' ),
	'description' => __( 'Upload an image of the individual being quoted in the testimonial. Ideally, this image should be 320x302px.', 'venture-lite-companion' ),
	'help'        => '',
	'section'     => 'fp-test',
	'default'     => '',
	'priority'    => 10,
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-test-title',
	'label'    => __( 'Testimonial - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-test',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the testimonial section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'     => 'textarea',
	'settings' => 'fp-test-description',
	'label'    => __( 'Testimonial', 'venture-lite-companion' ),
	'section'  => 'fp-test',
	'default'  => '',
	'priority' => 10,
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-test-tag',
	'label'    => __( 'Testimonial - Name', 'venture-lite-companion' ),
	'section'  => 'fp-test',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the name under the testimonial section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-test-tag-url',
	'label'    => __( 'Testimonial - Website Link', 'venture-lite-companion' ),
	'section'  => 'fp-test',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the link applied to the name above.', 'venture-lite-companion' ),
	'sanitize_callback' => 'venture_lite_companion_sanitize_url'
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-test-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-test',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'test',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );





Kirki::add_section( 'fp-news', array(
    'title'          => __( 'Frontpage News Section', 'venture-lite-companion' ),
    'description'    => '',
    'panel'          => '', 
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'radio-buttonset',
	'settings'    => 'fp-news-toggle',
	'label'       => __( 'News Row Status', 'venture-lite-companion' ),
	'section'     => 'fp-news',
	'default'     => '2',
	'priority'    => 1,
	'choices'     => array(
		'1'   => esc_attr__( 'Show', 'venture-lite-companion' ),
		'2'  => esc_attr__( 'Hide', 'venture-lite-companion' ),
	),
) );

Kirki::add_field( 'venture-lite-config', array(
	'type'        => 'custom',
	'settings'    => 'news-note',
	'label'       => __( 'About News Section', 'venture-lite-companion' ),
	'section'     => 'fp-news',
	'default'     => __( 'You can use this section as either a feed that displays 4 of your latest blog posts, or as your blog page itself (the # of posts specified in Settings > Reading > #2). If you want the Blog to be a separate page completely (and only show the first 4 posts on the frontpage feed), go to Settings > Reading and make sure Frontpage displays... A static page... and select the HOME page (and create a HOME page if you have not already). Then, create a BLOG page and set the BLOG page as the Posts page option in Settings > Reading. If you do not want the blog to be displayed separately, then set Frontpage displays... Your latest posts.', 'venture-lite-companion' ),
	'priority'    => 10,
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-news-title',
	'label'    => __( 'News - Main Title', 'venture-lite-companion' ),
	'section'  => 'fp-news',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the big text in the news section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-news-sub-title',
	'label'    => __( 'News - Sub Title', 'venture-lite-companion' ),
	'section'  => 'fp-news',
	'type'     => 'text',
	'priority' => 10,
	'default'  => '',
	'description'   => __( 'This is the smaller text in the news section. Leave blank to hide.', 'venture-lite-companion' ),
) );

Kirki::add_field( 'venture-lite-config', array(
	'settings' => 'fp-news-slug',
	'label'    => __( 'Navigation Menu ID', 'venture-lite-companion' ),
	'section'  => 'fp-news',
	'type'     => 'text',
	'priority' => 10,
	'default'  => 'news',
	'description'   => __( 'The frontpage section IDs (what shows up in the hover state and the address bar when clicked) have already been set to a default show in this field. If you would like to change the ID so that a different term comes up in the slug for that section (ie. http://example.com/#top instead of /#home), then change the term below for the corresponding section. You will also want to add the custom menu items in the Menus section of your dashboard (click "Links," then add the entire URL, such as http://example.com/#top). IMPORTANT: You must also add this term to the title field in the menu editor. If you do not see this field you may have to activate it by selecting the Screen Options tab in the top right of the page and then checking the Title Attribute box.', 'venture-lite-companion' ),
) );



// #################################################
// Some Custom Sanitize Functions
// #################################################

function venture_lite_companion_sanitize_url( $value ) {

    $value=esc_url( $value );

    return $value;

}

function venture_lite_companion_sanitize_email( $value ) {

    $value=sanitize_email( $value );

    return $value;

}

?>
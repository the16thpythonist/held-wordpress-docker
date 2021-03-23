<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



// #################################################
// Register widget area.
// #################################################

function venture_lite_companion_theme_widgets_init() {

    // frontpage - featured - left
    register_sidebar(array(
        'id' => 'frontpage-featured-left',
        'name' => __('Frontpage Featured Left', 'venture-lite-companion' ),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="featured-item-title">',
        'after_title' => '</h4>'
    ));

    // frontpage - featured - center left
    register_sidebar(array(
        'id' => 'frontpage-featured-center-left',
        'name' => __('Frontpage Featured Center Left', 'venture-lite-companion' ),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="featured-item-title">',
        'after_title' => '</h4>'
    ));
    
    // frontpage - featured - center right
    register_sidebar(array(
        'id' => 'frontpage-featured-center-right',
        'name' => __('Frontpage Featured Center Right', 'venture-lite-companion' ),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="featured-item-title">',
        'after_title' => '</h4>'
    ));
    
    // frontpage - featured - right
    register_sidebar(array(
        'id' => 'frontpage-featured-right',
        'name' => __('Frontpage Featured Right', 'venture-lite-companion' ),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h4 class="featured-item-title">',
        'after_title' => '</h4>'
    ));

    // frontpage - about
    register_sidebar(array(
        'id' => 'frontpage-about',
        'name' => __('Frontpage About', 'venture-lite-companion' ),
        'before_widget' => '<div class="row frontpage-about-row" data-sr="enter left and move 50px after 1s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>'
    ));
    
    // frontpage - team - left
    register_sidebar(array(
        'id' => 'frontpage-team-left',
        'name' => __('Frontpage Team Left', 'venture-lite-companion' ),
        'before_widget' => '<div class="team-item" data-sr="wait 0.3s, enter right and move 50px after 1s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="team-item-title">',
        'after_title' => '</h4>'
    ));

    // frontpage - team - center left
    register_sidebar(array(
        'id' => 'frontpage-team-center-left',
        'name' => __('Frontpage Team Center Left', 'venture-lite-companion' ),
        'before_widget' => '<div class="team-item" data-sr="wait 0.3s, enter right and move 50px after 1s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="team-item-title">',
        'after_title' => '</h4>'
    ));
    
    // frontpage - team - center right
    register_sidebar(array(
        'id' => 'frontpage-team-center-right',
        'name' => __('Frontpage Team Center Right', 'venture-lite-companion' ),
        'before_widget' => '<div class="team-item" data-sr="wait 0.3s, enter right and move 50px after 1s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="team-item-title">',
        'after_title' => '</h4>'
    ));
    
    // frontpage - team - right
    register_sidebar(array(
        'id' => 'frontpage-team-right',
        'name' => __('Frontpage Team Right', 'venture-lite-companion' ),
        'before_widget' => '<div class="team-item" data-sr="wait 0.3s, enter right and move 50px after 1s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="team-item-title">',
        'after_title' => '</h4>'
    ));    
    
    // frontpage - social
    register_sidebar(array(
        'id' => 'frontpage-social-media',
        'name' => __('Frontpage Social Media', 'venture-lite-companion' ),
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => ''
    ));

}
add_action( 'widgets_init', 'venture_lite_companion_theme_widgets_init' );



// #################################################
// Featured Content Widget
// #################################################

class venture_lite_companion_featured_content_widget extends WP_Widget {
    
    // Register widget
    function __construct() {
        parent::__construct(
          'venture-lite-companion-featured-content-widget', // Base ID
          'Venture Lite - Featured Content Widget', // Name
          array( 'description' => __('Display featured content boxes on the frontpage', 'venture-lite-companion' ))  // Description
        );
    }
    
    // Create output function
    public function widget($args, $instance) {
		echo $args['before_widget'];
		?>
            <a class="featured-item" href="<?php if ( ! empty( $instance['url'] ) ) { echo esc_url($instance['url']); } ?>" data-sr="wait 0.1s, enter top and move 50px after 1s">
                <i class="fa <?php if ( ! empty( $instance['faclass'] ) ) { echo esc_html($instance['faclass']); } ?>"></i>
                <?php if ( ! empty( $instance['title'] ) ) { 
                    echo $args['before_title'] . esc_html($instance['title']) . $args['after_title']; 
                } ?>
                <p class="featured-item-sub-title">
                    <?php if ( ! empty( $instance['description'] ) ) { 
                        echo esc_html($instance['description']); 
                    } ?>
                </p>
            </a>
		<?php
		echo $args['after_widget'];
    }  
    
    // Create widget form
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? esc_html($instance['title']) : __( 'New title', 'venture-lite-companion' );
		$description = ! empty( $instance['description'] ) ? esc_html($instance['description']) : __( 'New description text.', 'venture-lite-companion' );
		$faclass = ! empty( $instance['faclass'] ) ? esc_html($instance['faclass']) : __( 'fa-star', 'venture-lite-companion' );
		$url = ! empty( $instance['url'] ) ? esc_url($instance['url']) : __( '', 'venture-lite-companion' );
		?>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e( 'Title:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php _e( 'Description:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'description' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description' )); ?>" type="text" value="<?php echo esc_attr( $description ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'faclass' )); ?>"><?php _e( 'FontAwesome Class:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'faclass' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'faclass' )); ?>" type="text" value="<?php echo esc_attr( $faclass ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'url' )); ?>"><?php _e( 'URL:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'url' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'url' )); ?>" type="text" value="<?php echo esc_attr( $url ); ?>">
		</p>
		<?php 
	}
    
    // Save stuff
    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['description'] = ( ! empty( $new_instance['description'] ) ) ? sanitize_text_field($new_instance['description']) : '';
        $instance['faclass'] = ( ! empty( $new_instance['faclass'] ) ) ? esc_attr($new_instance['faclass']) : '';	
	    $instance['url'] = ( ! empty( $new_instance['url'] ) ) ? esc_url_raw($new_instance['url']) : '';
		return $instance;
	}
        
}

// Register featured content widget
function register_venture_lite_companion_featured_content_widget() {
    register_widget( 'venture_lite_companion_featured_content_widget' );
}
add_action( 'widgets_init', 'register_venture_lite_companion_featured_content_widget' );




// #################################################
// About Row Content Widget
// #################################################

class venture_lite_companion_about_content_widget extends WP_Widget {
    
    // Register widget
    function __construct() {
        parent::__construct(
          'venture-lite-companion-about-content-widget', // Base ID
          'Venture Lite - About Content Widget', // Name
          array( 'description' => __('Display about content boxes on the frontpage', 'venture-lite-companion' ))  // Description
        );
    }
    
    // Create output function
    public function widget($args, $instance) {
		echo $args['before_widget'];
		?>
            <div class="col-sm-6">
                <i class="fa <?php if ( ! empty( $instance['faclass-left'] ) ) { echo esc_html($instance['faclass-left']); } ?>"></i>
                <?php if ( ! empty( $instance['title-left'] ) ) { 
                    echo $args['before_title'] . esc_html($instance['title-left']) . $args['after_title']; 
                } ?>
                <p>
                    <?php if ( ! empty( $instance['description-left'] ) ) { 
                        echo esc_html($instance['description-left']); 
                    } ?>
                </p>
            </div>
            <div class="col-sm-6">
                <i class="fa <?php if ( ! empty( $instance['faclass-right'] ) ) { echo esc_html($instance['faclass-right']); } ?>"></i>
                <?php if ( ! empty( $instance['title-right'] ) ) { 
                    echo $args['before_title'] . esc_html($instance['title-right']) . $args['after_title']; 
                } ?>
                <p>
                    <?php if ( ! empty( $instance['description-right'] ) ) { 
                        echo esc_html($instance['description-right']); 
                    } ?>
                </p>
            </div>   
		<?php
		echo $args['after_widget'];
    }  
    
    // Create widget form
	public function form( $instance ) {
		$title_left = ! empty( $instance['title-left'] ) ? esc_html($instance['title-left']) : __( 'New title', 'venture-lite-companion' );
		$description_left = ! empty( $instance['description-left'] ) ? esc_html($instance['description-left']) : __( 'New description text.', 'venture-lite-companion' );
		$faclass_left = ! empty( $instance['faclass-left'] ) ? esc_html($instance['faclass-left']) : __( 'fa-star', 'venture-lite-companion' );
		$title_right = ! empty( $instance['title-right'] ) ? esc_html($instance['title-right']) : __( 'New title', 'venture-lite-companion' );
		$description_right = ! empty( $instance['description-right'] ) ? esc_html($instance['description-right']) : __( 'New description text.', 'venture-lite-companion' );
		$faclass_right = ! empty( $instance['faclass-right'] ) ? esc_html($instance['faclass-right']) : __( 'fa-star', 'venture-lite-companion' );
		?>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'title-left' )); ?>"><?php _e( 'Left Column Title:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title-left' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title-left' )); ?>" type="text" value="<?php echo esc_attr( $title_left ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'description-left' )); ?>"><?php _e( 'Left Column Description:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'description-left' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description-left' )); ?>" type="text" value="<?php echo esc_attr( $description_left ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'faclass-left' )); ?>"><?php _e( 'Left Column FontAwesome Class:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'faclass-left' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'faclass-left' )); ?>" type="text" value="<?php echo esc_attr( $faclass_left ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'title-right' )); ?>"><?php _e( 'Right Column Title:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title-right' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title-right' )); ?>" type="text" value="<?php echo esc_attr( $title_right ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'description' )); ?>"><?php _e( 'Right Column Description:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'description-right' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'description-right' )); ?>" type="text" value="<?php echo esc_attr( $description_right ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'faclass' )); ?>"><?php _e( 'Right Column FontAwesome Class:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'faclass-right' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'faclass-right' )); ?>" type="text" value="<?php echo esc_attr( $faclass_right ); ?>">
		</p>
		
		<?php 
	}
    
    // Save stuff
    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title-left'] = ( ! empty( $new_instance['title-left'] ) ) ? sanitize_text_field( $new_instance['title-left'] ) : '';
		$instance['description-left'] = ( ! empty( $new_instance['description-left'] ) ) ? sanitize_text_field($new_instance['description-left']) : '';
        $instance['faclass-left'] = ( ! empty( $new_instance['faclass-left'] ) ) ? esc_attr($new_instance['faclass-left']) : '';	
	    $instance['title-right'] = ( ! empty( $new_instance['title-right'] ) ) ? sanitize_text_field( $new_instance['title-right'] ) : '';
		$instance['description-right'] = ( ! empty( $new_instance['description-right'] ) ) ? sanitize_text_field($new_instance['description-right']) : '';
        $instance['faclass-right'] = ( ! empty( $new_instance['faclass-right'] ) ) ? esc_attr($new_instance['faclass-right']) : '';
		return $instance;
	}
        
}

// Register about row content widget
function register_venture_lite_companion_about_content_widget() {
    register_widget( 'venture_lite_companion_about_content_widget' );
}
add_action( 'widgets_init', 'register_venture_lite_companion_about_content_widget' );



// #################################################
// Team Row Content Widget
// #################################################

class venture_lite_companion_team_content_widget extends WP_Widget {
    
    // Register widget
    function __construct() {
        parent::__construct(
          'venture-lite-companion-team-content-widget', // Base ID
          'Venture Lite - Team Content Widget', // Name
          array( 'description' => __('Display team content boxes on the frontpage', 'venture-lite-companion' )) // Description
        );
    }
    
    // Create output function
    public function widget($args, $instance) {
		echo $args['before_widget'];
		?>
            <img class="img-responsive img-circle center-block" src="<?php if ( ! empty( $instance['imgurl184sq'] ) ) { echo $instance['imgurl184sq']; } ?>" />
            <h4 class="team-item-title">
                <?php if ( ! empty( $instance['name'] ) ) { 
                    echo esc_html($instance['name']); 
                } ?>            	
            </h4>
            <h5 class="team-item-sub-title">
                <?php if ( ! empty( $instance['title'] ) ) { 
                    echo esc_html($instance['title']); 
                } ?> 
            </h5>
            <p class="team-social-icons">
            	<?php if (!empty( $instance['social1']) && !empty( $instance['faclass1'])) { ?>
            		<a href="<?php echo esc_url($instance['social1']); ?>"><i class="fa <?php echo esc_html($instance['faclass1']); ?>"></i></a>
            	<?php } ?>
            	<?php if (!empty( $instance['social2']) && !empty( $instance['faclass2'])) { ?>
            		<a href="<?php echo esc_url($instance['social2']); ?>"><i class="fa <?php echo esc_html($instance['faclass2']); ?>"></i></a>
            	<?php } ?>
            	<?php if (!empty( $instance['social3']) && !empty( $instance['faclass3'])) { ?>
            		<a href="<?php echo esc_url($instance['social3']); ?>"><i class="fa <?php echo esc_html($instance['faclass3']); ?>"></i></a>
            	<?php } ?>
            </p>  
		<?php
		echo $args['after_widget'];
    }  
    
    // Create widget form
	public function form( $instance ) {
		$imgurl184sq = ! empty( $instance['imgurl184sq'] ) ? esc_url($instance['imgurl184sq']) : __( '', 'venture-lite-companion' );
		$name = ! empty( $instance['name'] ) ? esc_html($instance['name']) : __( '', 'venture-lite-companion' );
		$title = ! empty( $instance['title'] ) ? esc_html($instance['title']) : __( '', 'venture-lite-companion' );
		$social1 = ! empty( $instance['social1'] ) ? esc_url($instance['social1']) : __( '', 'venture-lite-companion' );
		$faclass1 = ! empty( $instance['faclass1'] ) ? esc_html($instance['faclass1']) : __( '', 'venture-lite-companion' );
		$social2 = ! empty( $instance['social2'] ) ? esc_url($instance['social2']) : __( '', 'venture-lite-companion' );
		$faclass2 = ! empty( $instance['faclass2'] ) ? esc_html($instance['faclass2']) : __( '', 'venture-lite-companion' );
		$social3 = ! empty( $instance['social3'] ) ? esc_url($instance['social3']) : __( '', 'venture-lite-companion' );
		$faclass3 = ! empty( $instance['faclass3'] ) ? esc_html($instance['faclass3']) : __( '', 'venture-lite-companion' );
		?>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'imgurl184sq' )); ?>"><?php _e( 'Headshot Image (184x184px):', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'imgurl184sq' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'imgurl184sq' )); ?>" type="text" value="<?php echo esc_attr( $imgurl184sq ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'name' )); ?>"><?php _e( 'Name:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'name' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'name' )); ?>" type="text" value="<?php echo esc_attr( $name ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e( 'Title:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'social1' )); ?>"><?php _e( 'Social Media Link #1:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'social1' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'social1' )); ?>" type="text" value="<?php echo esc_attr( $social1 ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'faclass1' )); ?>"><?php _e( 'FontAwesome Class #1:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'faclass1' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'faclass1' )); ?>" type="text" value="<?php echo esc_attr( $faclass1 ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'social2' )); ?>"><?php _e( 'Social Media Link #2:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'social2' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'social2' )); ?>" type="text" value="<?php echo esc_attr( $social2 ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'faclass2' )); ?>"><?php _e( 'FontAwesome Class #2:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'faclass2' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'faclass2' )); ?>" type="text" value="<?php echo esc_attr( $faclass2 ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'social3' )); ?>"><?php _e( 'Social Media Link #3:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'social3' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'social3' )); ?>" type="text" value="<?php echo esc_attr( $social3 ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'faclass3' )); ?>"><?php _e( 'FontAwesome Class #3:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'faclass3' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'faclass3' )); ?>" type="text" value="<?php echo esc_attr( $faclass3 ); ?>">
		</p>


		<?php 
	}
    
    // Save stuff
    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['imgurl184sq'] = ( ! empty( $new_instance['imgurl184sq'] ) ) ? esc_url_raw($new_instance['imgurl184sq']) : '';
		$instance['name'] = ( ! empty( $new_instance['name'] ) ) ? sanitize_text_field($new_instance['name']) : '';
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field($new_instance['title']) : '';
		$instance['social1'] = ( ! empty( $new_instance['social1'] ) ) ? esc_url_raw($new_instance['social1']) : '';
		$instance['faclass1'] = ( ! empty( $new_instance['faclass1'] ) ) ? esc_attr($new_instance['faclass1']) : '';
		$instance['social2'] = ( ! empty( $new_instance['social2'] ) ) ? esc_url_raw($new_instance['social2']) : '';
		$instance['faclass2'] = ( ! empty( $new_instance['faclass2'] ) ) ? esc_attr($new_instance['faclass2']) : '';
		$instance['social3'] = ( ! empty( $new_instance['social3'] ) ) ? esc_url_raw($new_instance['social3']) : '';
		$instance['faclass3'] = ( ! empty( $new_instance['faclass3'] ) ) ? esc_attr($new_instance['faclass3']) : '';
		return $instance;
	}
        
}

// Register widget
function register_venture_lite_companion_team_content_widget() {
    register_widget( 'venture_lite_companion_team_content_widget' );
}
add_action( 'widgets_init', 'register_venture_lite_companion_team_content_widget' );



// #################################################
// Social Media Row Content Widget
// #################################################

class venture_lite_companion_social_content_widget extends WP_Widget {
    
    // Register widget
    function __construct() {
        parent::__construct(
          'venture-lite-companion-social-content-widget', // Base ID
          'Venture Lite - Social Media Content Widget', // Name
          array( 'description' => __('Display social content boxes on the frontpage', 'venture-lite-companion' ))  // Description
        );
    }
    
    // Create output function
    public function widget($args, $instance) {
		echo $args['before_widget'];
		?>
			<div data-sr="wait 0.2s, scale up 25%">
	            <a href="<?php if ( ! empty( $instance['url'] ) ) { echo esc_url($instance['url']); } ?>">
	            	<i class="fa <?php if ( ! empty( $instance['faclass'] ) ) { echo esc_html($instance['faclass']); } ?>"></i><br>
	            	<span class="social-item-title h5">
		                <?php if ( ! empty( $instance['title'] ) ) { 
		                    echo $instance['title']; 
		                } ?> 	
	            	</span><br>
	            	<span class="social-item-sub-title h5">
		                <?php if ( ! empty( $instance['sub-title'] ) ) { 
		                    echo esc_html($instance['sub-title']); 
		                } ?>
	            	</span>
	            </a>  
            </div>
		<?php
		echo $args['after_widget'];
    }  
    
    // Create widget form
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? esc_html($instance['title']) : __( 'New title', 'venture-lite-companion' );
		$sub_title = ! empty( $instance['sub-title'] ) ? esc_html($instance['sub-title']) : __( '', 'venture-lite-companion' );
		$faclass = ! empty( $instance['faclass'] ) ? esc_html($instance['faclass']) : __( 'fa-star', 'venture-lite-companion' );
		$url = ! empty( $instance['url'] ) ? esc_url($instance['url']) : __( '', 'venture-lite-companion' );
		?>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php _e( 'Title:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'sub-title' )); ?>"><?php _e( 'Sub-title:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'sub-title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'sub-title' )); ?>" type="text" value="<?php echo esc_attr( $sub_title ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'faclass' )); ?>"><?php _e( 'FontAwesome Class:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'faclass' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'faclass' )); ?>" type="text" value="<?php echo esc_attr( $faclass ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr($this->get_field_id( 'url' )); ?>"><?php _e( 'URL:', 'venture-lite-companion' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'url' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'url' )); ?>" type="text" value="<?php echo esc_attr( $url ); ?>">
		</p>
		<?php 
	}
    
    // Save stuff
    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['sub-title'] = ( ! empty( $new_instance['sub-title'] ) ) ? sanitize_text_field($new_instance['sub-title']) : '';
        $instance['faclass'] = ( ! empty( $new_instance['faclass'] ) ) ? esc_attr($new_instance['faclass']) : '';	
	    $instance['url'] = ( ! empty( $new_instance['url'] ) ) ? esc_url_raw($new_instance['url']) : '';
		return $instance;
	}
        
}

// Register widget
function register_venture_lite_companion_social_content_widget() {
    register_widget( 'venture_lite_companion_social_content_widget' );
}
add_action( 'widgets_init', 'register_venture_lite_companion_social_content_widget' );
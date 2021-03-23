<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

        <!-- IMPORTING JQUERY SO IT CAN BE USED IN THE SCRIPTS -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <?php
        /**
         * Removed 02.05.2018
         * The html line in the header of the file, which was to include the javascript code for the header logo
         * animation and the dynamic resizing. The javascript is still being used although the file in now being
         * included into the html using the wordpress queueing mechanism from within the functions.php
         */
        ?>
        <?php wp_head(); ?>
	</head>
    <!-- REMEMBER THE BACKGROUND IMAGE HAS BEEN CODED OUT RIGHT HERE -->
	<body <?php body_class(); ?> data-spy="scroll" data-target=".navbar-default" style="background-image: none; background-color: ghostwhite">
		<header id="<?php if (nimbus_get_option('fp-banner-slug')=='') {echo "home";} else {echo esc_attr(nimbus_get_option('fp-banner-slug'));} ?>" >
			<div class="helmholtz-bar">
                <div class="helmholtz-img-container">
                    <a href="http://www.helmholtz.de" >
                        <img class="helmholtz-img" src="https://www.helmholtz.de/typo3conf/ext/dreipc_helmholtz/Resources/Public/assets/Images/logo/logo_helmholtz_EN.svg">
                    </a>
                </div>

                <div class="helmholtz-bar-inner">

                </div>
                <div class="helmholtz-bar-triangle">

                </div>
            </div>
            <div class="container">
				<div class="row">
					<div class="col-sm-6 col-sm-push-6">
                        <!-- Here was a social media bar once, but the project, does not need a social media bar -->
                        <!-- Leaving this empty for now -->
					</div>		
					<div class="col-sm-6  col-sm-pull-6">

                        <div class="logo-wrapper">
                            <div class="logo-letter-wrapper">
                                <p class="logo-letter">
                                    <b>M</b>
                                </p>
                            </div>
                            <div class="logo-letter-wrapper" id="logo-second-letter-wrapper">
                                <p class="logo-letter">
                                    <b>T</b>
                                </p>
                            </div>
                            <div class="logo-dts-wrapper">
                                <p class="logo-dts">
                                    <span><b>D</b></span><b>TS</b>
                                </p>
                            </div>
                            <div class="after-logo-box">

                            </div>
                        </div>

						<?php
						if ( function_exists( 'the_custom_logo' ) ) {
							if (has_custom_logo()){
								# the_custom_logo();
							} else {
								get_template_part( 'partials/textlogo');
							}
						} else {
							get_template_part( 'partials/textlogo');
						}
						?>
					</div>
				</div>	
			</div>
	    </header>
	    <nav class="primary-nav">
			<div class="container">
				<div class="row">
					<div class="col-sm-12">

						<?php get_template_part( 'partials/menu'); ?>

                        <!-- WIDGET AREA -->
                        <!-- This is an additional widget area in the header of the web page -->
                        <!-- Mainly, only for the search bar -->
                        <?php if ( is_active_sidebar('header_1') ): ?>
                            <div class="widget-header">
                                <?php dynamic_sidebar('header_1'); ?>
                            </div>
                        <?php endif; ?>
					</div>		
				<div>
			</div>
	    </nav>
	    <?php if (is_front_page() && !is_home() && !is_paged()) {
		    get_template_part( 'partials/frontpage','banner');
	    } ?>
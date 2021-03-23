<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
get_header(); ?>
<?php get_template_part( 'partials/subpage','banner'); ?>
<div class="sub-background">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="post-detail" id="singular-<?php the_ID(); ?>">
                    <?php if ( have_posts() ) :  ?> 
                        <?php while ( have_posts() ) : 
                            the_post(); ?>
                            <div class="archive-singular-wrap" id="singular-<?php the_ID(); ?>-lol">
                                <?php get_template_part( 'partials/img','750x420'); ?>
                                <div>

                                    <?php

                                    //if (get_post_type( get_the_ID() ) == 'post') {
                                    //    $hh_process->push_to_queue('')->save()->dispatch();
                                    //}
                                    //$hh_request->dispatch();
                                    // fetch_all();
                                    // THE TITLE
                                    // Previously the venture layout had the title above the actual post, overlaying
                                    // the background picture, that was above the content container
                                    // Changed: The title will be displayed inside the actual post body above the
                                    ?>
                                    <h1 class="page-title"><?php the_title() ?></h1>

                                    <?php
                                    // META INFORMATION
                                    // The venture layout had the following code to display the meta data for the post
                                    // between the title and the actual post content:

                                    //get_template_part( 'partials/meta');

                                    // Changed 10.April.2018:
                                    // No longer displays the meta data, as that is not necessary for the publications
                                    ?>

                                    <div class="full-detail"><?php the_content(); ?></div>
                	                <?php wp_link_pages(); ?>
                                    <?php if (is_page()){ wp_link_pages( array( 'before' => '<div class="wp_link_pages">' . __( 'Pages:', 'venture-lite' ),'after'  => '</div>',) ); } ?>
                                    <?php if ( has_tag() ) { ?>
                                        <hr>
                                        <?php the_tags( '<div class="tags"><span class="tag">', '</span><span class="tag">','</span></div>' ); ?> 
                                    <?php } ?>
                                    <?php if ( comments_open() || get_comments_number() != 0 ) { ?>
                                        <hr>
                                        <?php comments_template(); ?>
                                    <?php } ?>
                                </div>

                                <?php
                                // Added 02.April.2018
                                // Under each singular page there are now two links, that link to the previous/next
                                // page/post.

                                // Changed 10.April 2018
                                // Changed the text, that was displayed for the links from 'previous publication' and
                                // 'next publication' to plain 'previous' and 'next'
                                // Also using the unicode characters for the double arrow instead of two comparison
                                // characters.

                                // Changed 10.April 2018
                                // Fixed the problem of the links not working, by implementing the methods correctly
                                // Altough that does not allow for custom classes or ids.
                                ?>
                                <div class="navigation">
                                    <?php previous_post_link("%link", '&#x00AB prev', true) ?>
                                    <?php next_post_link("%link", 'next &#x00BB', true); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <p><?php _e('No posts found.', 'venture-lite' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-sm-4 sidebar-primary">
                <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>

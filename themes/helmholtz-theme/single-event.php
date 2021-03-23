
<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.06.18
 * Time: 11:36
 */
use the16thpythonist\Wordpress\Scopus\AuthorObservatory;
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
get_header(); ?>
<?php get_template_part( 'partials/subpage','banner'); ?>
<div class="sub-background">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="post-detail" id="singular-<?php the_ID(); ?>">
                    <?php if ( have_posts() ) :  ?>
                            <?php

                            // Getting the id of the current post
                            $id = get_the_ID();
                            // Creating a new event wrapper object for the post
                            $event = new Event($id);
                            ?>
                            <div class="archive-singular-wrap" id="singular-<?php echo $event->id; ?>">
                                <div>

                                    <h1 class="page-title"><?php echo $event->title ?></h1>

                                    <?php
                                    // META INFORMATION
                                    // The venture layout had the following code to display the meta data for the post
                                    // between the title and the actual post content:

                                    //get_template_part( 'partials/meta');

                                    // Changed 10.April.2018:
                                    // No longer displays the meta data, as that is not necessary for the publications
                                    ?>

                                    <div class="event-container">
                                        <?php
                                        // TODO: Add in the start time and the location
                                        ?>
                                    </div>

                                    <div class="full-detail">
                                        <?php echo $event->description ?>
                                    </div>

                                    <div class="link">
                                        <a class="btn btn-primary" href="<?php echo $event->url ?>">Visit Indico</a>
                                    </div>

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

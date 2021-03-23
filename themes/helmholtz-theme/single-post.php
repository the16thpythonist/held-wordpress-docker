<?php
use Log\LogPost;
use the16thpythonist\Wordpress\Scopus\PublicationPost;

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
                        <?php

                        // Creating a publication wrapper object from the given id
                        $id = get_the_ID();
                        $publication = new PublicationPost($id);
                        ?>
                        <div class="archive-singular-wrap" id="singular-<?php echo $publication->ID; ?>">
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

                                // Changed 03.June 2018
                                // Replaced 'the_title' with echoing the title attribute of the publication wrapper
                                // object of the given page
                                ?>
                                <h1 class="page-title"><?php echo $publication->title; ?></h1>

                                <?php
                                // Changed 03.June 2018
                                // The authors are no longer part of the actual post content, they are now post
                                // meta information/taxonomy, echoing them from the publication wrapper object

                                // Changed 11.06.2018
                                // Added the increment statement for the count variable, so that the loop actually
                                // terminates.
                                // Added a 'et al.' string after the list of authors, it the list is terminated
                                // prematurely
                                ?>
                                <div class="scopus-publication-authors">
                                    <?php
                                    $authors = $publication->getAuthors();
                                    $max = 20;
                                    $count = 0;
                                    foreach ($authors as $author) {
                                        if ($count >= $max) {
                                            echo ' et al.';
                                            break;
                                        } else {
                                            echo $author . ', ';
                                        }
                                        $count += 1;
                                    }
                                    ?>
                                </div>


                                <?php
                                // CHANGELOG

                                // Added 11.06.2018
                                // Added an additional element to the post page for the journal, that was missing

                                // Changed 11.06.2018
                                // Decided to put the actual journal name in cursive

                                if($publication->getJournal() != ''):
                                    ?>
                                    <div class="scopus-publication-journal">
                                        in
                                        <em>
                                            <?php
                                            // Calculating the year from the given publishing date
                                            $year = date('Y', strtotime($publication->published))
                                            ?>
                                            <?php echo $publication->getJournal() ?> (<?php echo $year ?>)
                                        </em>
                                    </div>
                                <?php endif ?>

                                <?php
                                // META INFORMATION
                                // The venture layout had the following code to display the meta data for the post
                                // between the title and the actual post content:

                                //get_template_part( 'partials/meta');

                                // Changed 10.April.2018:
                                // No longer displays the meta data, as that is not necessary for the publications

                                // Changed 03.June 2018
                                // The header 'abstract' is no longer part of the actual post content, so it is
                                // displayed seperately above the content.
                                // The content is no longer being displayed by 'the_content'. Instead the abstract
                                // attribute of the publication wrapper object is now being echoed.
                                ?>
                                <h4>Abstract</h4>
                                <div class="full-detail">
                                    <?php echo $publication->abstract; ?>
                                </div>

                                <?php
                                /*
                                 * Changed 03.06.2018
                                 * The "get it" link is now not longer a part of the actual post content, so it
                                 * is being displayed in a separate container below the actual content. The URL of
                                 * the link is still the dx.doi.org link to the article.
                                 *
                                 * Changed 10.07.2018
                                 * Added an additional link button to the page, which links to the KITOpen page of the
                                 * publication, in case one exists.
                                 * Put both of the buttons into an additional div 'button-container', which will be
                                 * used to align them side by side
                                 */
                                ?>
                                <div class="button-container">
                                    <div class="scopus-link-container">
                                        <a class="btn btn-primary" href="<?php echo $publication->getURL();?>">Get it</a>
                                    </div>

                                    <?php
                                    if ($publication->isKITOpen()):
                                        ?>
                                        <div class="kitopen-link-container">
                                            <a class="btn btn-primary" href="<?php echo $publication->getKITOpenURL(); ?>">KITOpen</a>
                                        </div>
                                    <?php endif; ?>
                                </div>

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


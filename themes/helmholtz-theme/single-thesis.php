
<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.06.18
 * Time: 11:36
 */
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
                        //hh_add_global_category('Publications');
                        // Getting the id of the current post
                        $post_id = get_the_ID();
                        // Creating a new Wrapper object for the post. The wrapper object makes it easier to access the
                        // custom meta data of the post, by having them stored in its attributes
                        // Changed 17.12.2019
                        // The "Thesis" class is deprecated and is being replaced by the "ThesisPost" class.
                        $thesis_post = new ThesisPost($post_id);
                        ?>
                        <div class="archive-singular-wrap" id="singular-<?php echo $thesis_post->ID; ?>">
                            <div>

                                <h1 class="page-title"><?php echo $thesis_post->title ?></h1>

                                <?php
                                // META INFORMATION
                                // The venture layout had the following code to display the meta data for the post
                                // between the title and the actual post content:

                                //get_template_part( 'partials/meta');

                                // Changed 10.April.2018:
                                // No longer displays the meta data, as that is not necessary for the publications
                                ?>

                                <!-- The Author of the Thesis -->
                                <div class="thesis-author-wrapper">
                                    <em><?php echo $thesis_post->getAuthorName(); ?></em>
                                </div>

                                <div class="thesis-information-wrapper">
                                    <?php echo 'PhD thesis, '. $thesis_post->department . ', ' . $thesis_post->university . ', ' . $thesis_post->published?>
                                </div>

                                <!-- Adding a header, that signals the following text to be the abstract of the thesis -->
                                <h4>Abstract</h4>
                                <div class="full-detail">
                                    <?php echo $thesis_post->abstract; ?>
                                </div>

                                <!-- The two assessors of the thesis -->
                                <br>
                                <div class="thesis-assessor-wrapper">
                                    <strong>First assessor:</strong> <?php echo $thesis_post->first_assessor; ?>
                                    <br>
                                    <strong>Second assessor:</strong> <?php echo $thesis_post->second_assessor; ?>
                                </div>
                                <p>
                                    Date of the exam: <?php echo $thesis_post->date; ?>
                                </p>
                                <br>
                                <div class="link">
                                    <a class="btn btn-primary" href="<?php echo $thesis_post->url; ?>">Get it</a>
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
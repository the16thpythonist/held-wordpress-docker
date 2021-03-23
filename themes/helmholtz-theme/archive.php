<?php get_header(); ?>
<?php get_template_part( 'partials/subpage','banner');

use the16thpythonist\Wordpress\Scopus\PublicationPost;
?>
<div class="sub-background">
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="post-detail">

                    <!-- This header was added by the helmholtz child theme -->
                    <div class="category-header">
                        <h2>
                            <?php
                            // Added 02.April 2018:
                            // Displays the title of the category above the list of posts to that category, so that
                            // the user knows at which kinds of publications he is actually looking at
                            single_cat_title()
                            ?>
                        </h2>

                        <?php
                        // Added 02.April 2018:
                        // Using the Advanced Custom Field plugin, getting the custom field from the category for the
                        // link to the static page, that describes the research associated with the category

                        // Added 10.April 2018:
                        // Using ACF, getting the description, that is to be displayed for a small explanation of
                        // the research topic described by that category

                        // Changed 10.April 2018:
                        // Combined the php tag for both the description and the link, displaying the description first
                        // and linking to the static page via a 'read more' at the end of the description.
                        // Also the link will now only be displayed if there already exists a description
                        $queriedObject = get_queried_object();
                        $categoryDescription = get_post_field('description', $queriedObject);
                        $categoryLink = get_post_field('link', $queriedObject);
                        if ($categoryDescription != false):
                        ?>
                            <p>
                                <?php echo $categoryDescription; ?>
                                <?php if($categoryLink != false): ?>
                                    <a href="<?php echo $categoryLink; ?>">Read more</a>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>


                    <!-- So this apparently loops through all the posts and displays them -->
                    <?php if ( have_posts() ) :  ?>
                        <?php while ( have_posts() ) :
                            the_post(); ?>
                            <!-- These are the div wrappers, that are directly placed into the container for all the posts -->
                            <div class="archive-singular-wrap" id="singular-<?php the_ID(); ?>">
                                <!-- This would display wort of a thumbnail before the actual text if the post for each post -->
                                <?php //get_template_part( 'partials/img','750x420'); ?>
                                <div>

                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                                    <?php if(hh_plugin_available()): ?>
                                        <div class="scopus-publication-authors">
                                            <?php
                                            // Changed 13.01.2019
                                            // Replaced the outdated "publication" wrapper with the usage of the
                                            // "PublicationPost" class
                                            $publication = new PublicationPost(get_the_ID());
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
                                        <?php if($publication->getJournal() != ''): ?>
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
                                    <?php endif; ?>

                                    <?php
                                    // THE META DATA
                                    // This line of code was used to display the metadata: author and data for the post
                                    // between the title and the short abstract:

                                    //get_template_part( 'partials/meta');

                                    // Changed 10.April 2018
                                    // Removed the meta data completely because it is not needed for displaying a
                                    // publication, that contains the date in its own content already
                                    ?>
                                    <!-- Here is a decision made, whether the post is too long and only a excerpt and a read more link -->
                                    <!-- Are being displayed or if the post can be displayed as a whole -->
                                    <?php if (nimbus_get_option('full-excerpt')=="2") { ?>
                                        <p class="excerpt"> <?php echo get_the_excerpt() ?></p>
                                        <p class="archive-link-button"><a href="<?php the_permalink(); ?>"><?php _e('Read More >>', 'venture-lite' ); ?></a></p>
                                    <?php } else { ?>
                                        <div class="full-detail"><?php the_content(); ?></div>
                                    <?php } ?>

                                    <?php
                                    // Added 03.June 2018
                                    // Since the get it button is no longer part of the actual post content, it is being
                                    // separately displayed beneath the actual content, by getting the url from the
                                    // publication wrapper for the given post.

                                    // Changed 11.06.2018
                                    // Added an if condition, that checks, if the helmholtz plugin is actually installed
                                    // for backwards comp.

                                    // Changed 13.01.2019
                                    // Replaced the outdated "publication" wrapper with the usage of the
                                    // "PublicationPost" class
                                    if (hh_plugin_available()):
                                    ?>
                                        <?php
                                        $id = get_the_ID();
                                        $publication = new PublicationPost($id);
                                        ?>
                                        <a class="btn btn-primary" href="<?php echo $publication->getURL() ?>">Get it</a>
                                    <?php endif; ?>

                                </div>
                            </div>
                        <?php endwhile; ?>
                        <div class="paginate_links_wrap">
                            <?php
                            global $wp_query;
                            $big = 999999999;
                            echo paginate_links( array(
                                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                'format' => '?paged=%#%',
                                'current' => max( 1, get_query_var('paged') ),
                                'total' => $wp_query->max_num_pages
                            ) ); ?>
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
<?php get_footer()?>

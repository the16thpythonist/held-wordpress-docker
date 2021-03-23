<?php
/**
 * This file is the template file, for displaying the search results.
 *
 *
 * CHANGELOG
 *
 * Added 26.06.2018
 *
 * @since 0.0.6.10
 */
use the16thpythonist\Wordpress\Scopus\PublicationPost;

get_header();
get_template_part( 'partials/subpage','banner');
?>
    <div class="sub-background">
        <div class="container">
            <div class="row">
                <div class="col-sm-8">
                    <div class="post-detail">

                        <!-- This header was added by the helmholtz child theme -->
                        <div class="category-header">
                            <h2>
                                Search results for <?php echo '"'. $_GET['s'] .'"'; ?>
                            </h2>
                        </div>


                        <!-- So this apparently loops through all the posts and displays them -->
                        <?php if ( have_posts() ) :  ?>
                            <?php while ( have_posts() ) :
                                the_post(); ?>
                                <?php
                                $post_id = get_the_ID();
                                $post = get_post($post_id);
                                $post_type = $post->post_type;
                                $link = get_the_permalink();
                                if ($post_type == 'page' || $post_type == 'author') {
                                    continue;
                                }
                                ?>
                                <!-- These are the div wrappers, that are directly placed into the container for all the posts -->
                                <div class="archive-singular-wrap" id="singular-<?php the_ID(); ?>">
                                    <!-- This would display wort of a thumbnail before the actual text if the post for each post -->
                                    <?php //get_template_part( 'partials/img','750x420'); ?>
                                    <div>
                                        <?php if(hh_plugin_available()): ?>

                                            <?php if($post_type == 'post'): ?>
                                                <?php
                                                $scopus_api = new \Scopus\ScopusApi(API_KEY);
                                                $publication = new PublicationPost($post_id);
                                                ?>

                                                <h3>
                                                    <em style="color: darkslategrey;">Publication:</em>
                                                    <a href="<?php echo $link; ?>"><?php echo $publication->title; ?></a>
                                                </h3>
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
                                                <?php /*
                                                Changed 15.12.2019
                                                Had to add the "esc_html__" because I had the case, that a post content
                                                actually contained html and that fucked up the whole page...
                                                */ ?>
                                                <div class="description">
                                                    <?php echo esc_html__($publication->abstract); ?>
                                                </div>
                                                <a class="btn btn-primary" href="<?php echo $publication->getURL(); ?>">Get it</a>
                                            <?php endif; ?>

                                            <?php if($post_type == 'highlight'):?>
                                                <h3>
                                                    <em style="color: darkslategrey;">Highlight:</em>
                                                    <a href="<?php echo $link; ?>"><?php echo $post->post_title; ?></a>
                                                </h3>
                                                <div class="description">
                                                    <?php echo $post->post_excerpt; ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if($post_type == 'thesis'): ?>
                                                <?php
                                                $thesis = new Thesis($post_id);
                                                ?>
                                                <h3>
                                                    <em style="color: darkslategrey;">PhD Thesis:</em>
                                                    <a href="<?php echo $link; ?>"><?php echo $thesis->title ?></a>
                                                </h3>
                                                <div class="scopus-publication-authors">
                                                    <em>
                                                        <?php echo $thesis->getAuthorIndexedName(); ?>
                                                    </em>
                                                    <?php echo ' (' .$thesis->getPublishingYear() . ')'; ?>
                                                </div>
                                                <div class="description">
                                                    <?php echo $thesis->abstract; ?>
                                                </div>
                                                <a class="btn btn-primary" href="<?php echo $thesis->url; ?>">Get it</a>
                                            <?php endif; ?>

                                            <?php if($post_type == 'event'): ?>
                                                <?php
                                                $event = new Event($post_id);

                                                ?>
                                                <h3>
                                                    <em style="color: darkslategrey;">Event:</em>
                                                    <a href="<?php echo $link; ?>"><?php echo $event->title; ?></a>
                                                </h3>
                                                <div class="scopus-publication-authors">
                                                    Starting at <em><?php echo $event->getStartingDate(); ?></em> in
                                                    "<em><?php echo $event->location; ?></em>"
                                                </div>
                                                <div class="description">
                                                    <?php echo $event->description; ?>
                                                </div>
                                                <a class="btn btn-primary" href="<?php echo $event->url ?>">Visit Indico</a>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

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
<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.06.18
 * Time: 13:55
 *
 * CHANGELOG
 *
 * Added 19.06.2018
 */
// Optionally these taxonomy queries are used to filter by given category in case a category is given
$tax_query = array();

if ($args['category'] != '') {
    $tax_query[] = array(
        'taxonomy' => 'category',
        'terms' => $args['category'],
        'field' => 'slug',
        'operator' => 'IN'
    );
}

// Getting all the highlight posts
$get_post_args = array(
    'post_type' => 'highlight',
    'numberposts' => $args['max']
);
$highlight_posts = get_posts($get_post_args);

/* @var $highlight_post WP_Post */
?>
<div class="display-post-listing">
    <?php foreach($highlight_posts as $highlight_post): ?>

        <?php
        $post_id = $highlight_post->ID;
        $highlight_title = $highlight_post->post_title;
        $highlight_link = get_permalink($post_id);
        $highlight_excerpt = $highlight_post->post_excerpt;
        ?>

        <div class="listing-item">
            <a class="title" href="<?php echo $highlight_link; ?>">
                <?php echo $highlight_title; ?>
            </a>
            <span class="excerpt">
                <?php echo $highlight_excerpt; ?>
            </span>
        </div>

    <?php endforeach; ?>
</div>
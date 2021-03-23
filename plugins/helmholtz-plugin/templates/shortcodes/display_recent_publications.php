<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 12.06.18
 * Time: 09:23
 *
 * CHANGELOG
 *
 * Changed 18.05.2018
 *
 * Added if conditionals, that check if either the 'category' or 'selection' parameters have been passed to the
 * shortcode and if that is the case adds a special restiction to the posts to be retrieved from the system to match
 * the parameter terms given.
 */

use the16thpythonist\Wordpress\Scopus\PublicationPost;

$tax_query = array();
if ($category != '') {
    $tax_query[] = array(
        'taxonomy' => 'category',
        'terms' => $category,
        'field' => 'slug',
        'operator' => 'IN'
    );
}
if ($selection != '') {
    $tax_query[] = array(
        'taxonomy' => 'selection',
        'terms' => $selection,
        'field' => 'name',
        'operator' => 'IN'
    );
}

// Getting all the recent posts in descending order
$args = array(
    'numberposts' => $max,
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'tax_query' => $tax_query
);
$publication_posts = get_posts($args);
// Creating a list of publication wrapper objects from those posts
$scopus_api = new \Scopus\ScopusApi(API_KEY);
$publications = array();
foreach ($publication_posts as $publication_post) {
    $post_id = $publication_post->ID;
    $publication = new PublicationPost($post_id);
    $publications[] = $publication;
}
/* @var $publication PublicationPost */
// Getting the category name
?>

<div class="display-posts-listing">
    <?php foreach($publications as $publication): ?>
        <?php
        $link = get_the_permalink($publication->ID);
        ?>
        <div class="listing item">
            <a class="title" href="<?php echo $link ?>"><?php echo $publication->title ?></a>
            <span class="excerpt"><?php echo ' - ' . $publication->getAuthors()[0] . ' et al., in <em>' . $publication->getJournal() . '</em> (' . $publication->published . ')' ?></span>
        </div>
    <?php endforeach; ?>
</div>
<?php if($category != '' && term_exists($category, 'category')): ?>
    <?php
    // Getting the name of the category
    /* @var $term WP_Term */
    $term = get_term_by('slug', $category, 'category');
    $category_name = $term->name;
    $category_link = get_term_link($term->term_id);
    ?>
    <a href="<?php echo $category_link; ?>">More publications on "<?php echo $category_name; ?>"</a>
<?php endif; ?>

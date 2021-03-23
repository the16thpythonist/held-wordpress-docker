<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.06.18
 * Time: 12:54
 */

$args = array(
    'number_posts' => $max,
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'tax_query' => array(
        array(
            'taxonomy' => 'selection',
            'terms' => $selection,
            'field' => 'name',
            'operator' => 'IN'
        )
    )
);
$publication_posts = get_posts($args);

$scopus_api = new \Scopus\ScopusApi(API_KEY);
$publications = array();
foreach ($publication_posts as $publication_post) {
    $post_id = $publication_post->ID;
    $publication = new Publication($scopus_api, $post_id);
    $publications[] = $publication;
}
?>

<ul class="display-posts-listing">
    <?php foreach($publications as $publication): ?>
        <?php
        $link = get_the_permalink($publication->id);
        ?>
        <li class="listing item">
            <a class="title" href="<?php echo $link ?>"><?php echo $publication->title ?></a>
            <span class="excerpt"><?php echo ' - ' . $publication->authors[0] . ' et al., in <em>' . $publication->journal . '</em> (' . $publication->published . ')' ?></span>
        </li>
    <?php endforeach; ?>
</ul>

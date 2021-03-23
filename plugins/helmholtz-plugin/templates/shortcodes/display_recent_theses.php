<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 12.06.18
 * Time: 09:44
 *
 * CHANGELOG
 *
 * Added 12.06.2018
 */

$args = array(
    'numberposts' => $max,
    'post_type' => 'thesis',
    'orderby' => 'date',
    'order' => 'DESC'
);
$thesis_posts = get_posts($args);

$theses = array();
foreach($thesis_posts as $thesis_post) {
    $post_id = $thesis_post->ID;
    $thesis = new Thesis($post_id);
    $theses[] = $thesis;
}

/* @var $thesis Thesis */
?>

<div class="display-posts-listing">
    <?php foreach ($theses as $thesis): ?>
        <?php
        $link = get_the_permalink($thesis->id);
        $content = $thesis->getAuthorIndexedName() . ', PhD thesis, ' . $thesis->institute . ', ' . $thesis->university . ', ' . $thesis->getPublishingYear();
        ?>
        <div>
            <a class="title" href="<?php echo $link; ?>"><?php echo $thesis->title ?></a>
            <span><?php echo ' - ' . $content ?></span>
        </div>
    <?php endforeach; ?>
</div>


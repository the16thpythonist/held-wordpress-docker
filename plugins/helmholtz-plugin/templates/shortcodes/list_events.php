<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 11.06.18
 * Time: 17:49
 *
 * Changelog
 *
 * Added 11.06.2018
 *
 * Changed 12.06.2018
 * Before the description is being used to generate an excerpt, it is being sanitized to remove any bold or cursive
 * html tags.
 */

// Getting all the event posts
$args = array(
    'numberposts' => -1,
    'post_type' => 'event'
);
$event_posts = get_posts($args);

// Creating a list of event wrapper objects from each of these post objects
/* @var $event_post WP_Post */
$events = array();
foreach ($event_posts as $event_post) {
    $post_id = $event_post->ID;
    $event = new Event($post_id);
    $events[] = $event;
}

// Sorting these events by their starting date
function eventCompare($a, $b) {
    /* @var $a Event */
    /* @var $b Event */
    $starting_time_a = strtotime($a->start);
    $starting_time_b = strtotime($b->start);
    $time_difference = $starting_time_b - $starting_time_a;
    return $time_difference;
}
usort($events, 'eventCompare');

$count = 0;
/* @var $event Event */
?>

<div class="display-posts-listing">
    <?php foreach ($events as $event): ?>
        <?php
        if ($count >= $max) {
            break;
        }
        // A lot of event descriptions have been written in html, which means, they might still contain unwanted tags
        // for bold and cursive text -> Sanitizing those first
        $sanitized_description = sanitize_textarea_field($event->description);
        $excerpt = substr($sanitized_description, 0, 300) . '...';
        $count += 1;
        ?>
        <div class="listing-item">
            <a class="title" href="<?php echo get_the_permalink($event->id)?>"><?php echo $event->title?></a>
            <span> - </span>
            <span class="excerpt"><?php echo $event->start . ', ' . $event->location . '. ' . $excerpt ?></span>
        </div>
    <?php endforeach; ?>
</div>

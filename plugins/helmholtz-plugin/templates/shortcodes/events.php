<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 03.06.18
 * Time: 13:24
 */

/* @var $event_post WP_Post */
/* @var $event Event */

$args = array(
    'numberposts' => $max,
    'post_type' => 'event'
);
$event_posts = get_posts($args);
$events = array();
foreach ($event_posts as $event_post) {
    $post_id = $event_post->ID;
    $event = new Event($post_id);
    $events[] = $event;
}
// Sorting the events by starting time
function hh_event_sort(Event $a, Event $b) {
    $time_a = strtotime($a->start);
    $time_b = strtotime($b->start);
    $time_difference = $time_b - $time_a;
    return $time_difference;
}
usort($events, "hh_event_sort");

$count = 0

?>

<div class="rpfc-container">
    <ul>
        <?php foreach ($events as $event):?>
            <?php $start_date = date('d.F Y', strtotime($event->start))?>
            <li>
                <a href="<?php echo get_post_permalink($event->id)?>"><?php echo $event->title?></a><?php echo ' (' . $start_date . ')'?>
            </li>
        <?php endforeach ?>
    </ul>
</div>

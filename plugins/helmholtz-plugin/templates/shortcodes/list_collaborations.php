<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 19.07.18
 * Time: 13:52
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 */

/*
 * First we need all the terms of the relevant collaborations in an array. The object for the "NONE"
 * Term is being separately fetched, because its id has to be known for the arguments of the "get_terms"
 * function already, because that term has to be excluded.
 */
$none_collaboration_term = get_term_by('slug','none', 'collaboration');
$args = array(
    'taxonomy'          => 'collaboration',
    'orderby'           => 'name',
    'exclude'           => array($none_collaboration_term->term_id),
);
$collaboration_terms = get_terms($args);

/* @var $collaboration_term WP_Term */
?>
<ul>
    <?php foreach ($collaboration_terms as $collaboration_term): ?>
        <?php $link = get_category_link($collaboration_term->term_id); ?>
        <li>
            <a href="<?php echo $link; ?>">
                <?php echo $collaboration_term->name; ?>
            </a><?php echo '(' . $collaboration_term->count . ')'; ?>
        </li>
    <?php endforeach; ?>
</ul>

<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 12:39
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 */

/*
 * The category_count_map is supposed to be an associative array, which holds the category name as the key and an array
 * with the post count and the array id as the value. It will be used in the main loop to create the HTML elements.
 *
 * The object for the term "Publications" is needed because all the actual categories are the child terms to this one
 * particular term
 */
$category_count_map = array();
$category_term_publication = get_term_by('slug', 'publications', 'category');
$category_ids = get_term_children($category_term_publication->term_id, 'category');
// var_dump($categories);
/* @var $category WP_Term */
foreach ($category_ids as $term_id) {
    /*
     * The category term has to be fetched here, because the "get_term_children" function only returns the
     * ids of the child terms.
     *
     * For the actual query to get all the posts belonging to that category it is important to only take those
     * publications, that do not belong to any collaboration ("NONE" collaboration term) as all the collaboration
     * papers will be listed separately on the website
     */
    $category = get_term_by('term_id', $term_id, 'category');
    $args = array(
        'post_type'         => 'post',
        'post_status'       => 'publish',
        'posts_per_page'    => -1,
        'category_name'     => $category->slug,
        'tax_query'         => array(
            array(
                'taxonomy'      => 'collaboration',
                'field'         => 'name',
                'terms'         => 'NONE',
                'operator'      => 'IN'
            )
        )
    );
    $query = new WP_Query($args);
    $post_count = $query->post_count;
    if ($post_count !== 0) {
        $category_count_map[$category->name] = array(
            'count'     => $post_count,
            'id'        => $category->term_id,
        );
    }
}
/*
 * By this, the categories will be displayed in alphabetical order, because the array is sorted by the keys and the
 * keys of this array are the category names.
 */
ksort($category_count_map);
?>

<ul>
    <?php foreach ($category_count_map as $category_name => $array):?>
        <?php $link = get_category_link($array['id']); ?>
        <li class="cat-item">
            <a href="<?php echo $link; ?>">
                <?php echo $category_name; ?>
            </a><?php echo '(' . $array['count'] . ')'; ?>
        </li>
    <?php endforeach; ?>
</ul>

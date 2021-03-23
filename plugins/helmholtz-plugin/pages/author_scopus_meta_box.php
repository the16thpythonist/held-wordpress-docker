<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 24.05.18
 * Time: 09:30
 *
 * CHANGELOG
 *
 * Changed 28.06.2018
 * Changed the way the author affiliations were put in. Previously the author affiliations were fetched on the server
 * side before the page was loaded and then simply displayed with their name and id, so that the user could input the
 * ids for black and whitelist into comma separated text input fields.
 * Now a widget is being displayed, that lets the user just tick off which ones belong to the whitelist and which to
 * the blacklist. This also means the fetching of the affiliations, the display of those and the two text inputs were
 * removed. The actual widget is being assembled via AJAX after the page has loaded (The load time due to the AJAX
 * requests were a big disadvantage)
 */

$post_id = $post->ID;

// Getting the meta values if they exist
$keys = array('first_name', 'last_name', 'scopus_author_id', 'categories');
foreach ($keys as $key) {
    if (metadata_exists('post', $post_id, $key)) {
        ${'' . $key} = get_post_meta($post_id, $key, true);
    } else {
        ${'' . $key} = '';
    }
}

?>

<div class="scopus-meta-box-wrapper">
    <?php foreach ($keys as $key): ?>
        <div class="scopus-input-wrapper">
            <p><?php echo $key?>:</p>
            <input type="text" id="<?php echo $key;?>" name="<?php echo $key?>" title="<?php echo $key?>" value="<?php echo ${'' . $key} ?>">
        </div>
    <?php endforeach; ?>

    <div id="affiliation-wrapper">
        <div class="affiliation-caption-row">
            <p class="first">Affiliation name</p>
            <p>whitelisted</p>
            <p>blacklisted</p>
        </div>
    </div>
</div>

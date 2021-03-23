<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 11.06.18
 * Time: 15:23
 *
 * CHANGELOG
 *
 * Added 11.06.2018
 */
$post_id = $post->ID;

// An associative array, that holds all the values of the post meta data as the value. First initialized with the
// default values
$default = array(
    'first_name'            => 'Max',
    'last_name'             => 'Mustermann',
    'university'            => 'Karlsruher Institut der Technologie',
    'institute'             => 'IPE',
    'published'             => '2018',
    'first_assessor'        => 'Prof. Dr. A. LastName',
    'second_assessor'       => 'Prof. Dr. B. LastName',
    'url'                   => 'www.google.com',
    'date'                  => '2018'
);
// Now, that the array is initialized with default data it is save to access any of those fields, but none of the values
// has any meaning. Now we attempt to get the actual values from the post meta data
$content = array();
foreach ($default as $key => $value) {
    if (metadata_exists('post', $post_id, $key)) {
        // In case the data exists (which means someone must have pressed save already at least once) this info is being
        // prioritized over the default value
        $content[$key] = get_post_meta($post_id, $key, true);
    } else {
        $content[$key] = $value;
    }
}

?>

<div class="helmholtz-meta-box-wrapper">
    <!-- Information about the author of the thesis -->
    <h4 class="helmholtz-meta-box-title">Author Details</h4>
    <div class="helmholtz-input-wrapper">
        <p>First Name:</p>
        <input type="text" name="first_name" title="first_name" value="<?php echo $content['first_name'] ?>">
    </div>
    <div class="helmholtz-input-wrapper">
        <p>Last Name:</p>
        <input type="text" name="last_name" title="last_name" value="<?php echo $content['last_name'] ?>">
    </div>
    <!-- Information about the thesis itself, such as the university, the publish date, where to find it etc. -->
    <h4 class="helmholtz-meta-box-title">Thesis Details</h4>
    <div class="helmholtz-input-wrapper">
        <p>University:</p>
        <input type="text" name="university" title="university" value="<?php echo $content['university'] ?>">
    </div>
    <div class="helmholtz-input-wrapper">
        <p>Department:</p>
        <input type="text" name="institute" title="institute" value="<?php echo $content['institute'] ?>">
    </div>
    <div class="helmholtz-input-wrapper">
        <p>Publishing date:</p>
        <input type="text" name="published" title="published" value="<?php echo $content['published'] ?>">
    </div>
    <div class="helmholtz-input-wrapper">
        <p>First Assessor:</p>
        <input type="text" name="first_assessor" title="first_assessor" value="<?php echo $content['first_assessor'] ?>">
    </div>
    <div class="helmholtz-input-wrapper">
        <p>Second Assessor:</p>
        <input type="text" name="second_assessor" title="second_assessor" value="<?php echo $content['second_assessor'] ?>">
    </div>
    <div class="helmholtz-input-wrapper">
        <p>Date of the exam:</p>
        <input type="text" name="date" title="date" value="<?php echo $content['date'] ?>">
    </div>
    <div class="helmholtz-input-wrapper">
        <p>Where to get it:</p>
        <input type="text" name="url" title="url" value="<?php echo $content['url'] ?>">
    </div>
</div>



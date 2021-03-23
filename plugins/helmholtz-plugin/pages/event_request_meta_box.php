<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 16.07.18
 * Time: 13:11
 */

/*
 * There is supposed to be a drop down selection of all the available indico sites
 * so the first thing we have to do is to get all the available sites from the corresponding array
 */
$indico_sites = array_keys(INDICO_CATEGORIES);


?>

<div class="indico-event-meta" style="display:flex;flex-direction: column;">
    <p>Select the website from which to import the event:</p>
    <select title="indico-url" name="indico-url" class="indico-url">
        <?php
        foreach ($indico_sites as $url):
        ?>
            <option value="<?php echo $url; ?>"><?php echo $url; ?></option>
        <?php endforeach; ?>
    </select>
    <div style="display:flex;flex-direction:row;height:30px;align-items:center;margin-top:10px;">
        <p style="margin:0px;margin-right:15px;">Event ID:</p>
        <input title="event-id" name="event-id" class="event-id" type="text" style="flex-grow: 2;">
    </div>
</div>

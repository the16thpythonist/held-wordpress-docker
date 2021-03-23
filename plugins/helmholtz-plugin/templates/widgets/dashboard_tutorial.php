<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 14.08.18
 * Time: 13:23
 */


// the uri for getting to the "Create new post page"
$create_event_uri = get_site_url(null, '/wp-admin/post-new.php?post_type=event');
// the url for getting to the "Create new Highlight page"
$create_highlight_uri = get_site_url(null, '/wp-admin/post-new.php?post_type=highlight');
// the url for getting to the "Create new PhD Thesis page"
$create_thesis_uri = get_site_url(null, '/wp-admin/post-new.php?post_type=thesis');
// the url for the commands page
$commands_uri = get_site_url(null, '/wp-admin/admin.php?page=background-commands');
// The url, which leads to the overview of all the logs
$logs_uri = get_site_url(null, '/wp-admin/edit.php?post_type=hh_log');

$count = 1;
?>


<div id="tutorial-container">

    <div class="item-container">
        <h2><?php echo $count; $count++;?>. Adding a new Highlight</h2>

        <p>
            To add a new Highlight, go to the <a href="<?php echo $create_highlight_uri; ?>">Create new Highlight</a>
            page on the held admin area. <br>
            Enter the title into the designated input field and the description/content of the highlight into the text
            editor beneath.<br>
            Use the "Excerpt" input to write a short summary of the highlight, that is supposed to be displayed, when
            the highlight post is being featured on the home page of the held website.
        </p>

    </div>

    <div class="item-container">
        <h2><?php echo $count; $count++;?>. Adding a new PhD Thesis</h2>

        <p>
            To add a post about a PhD thesis, go to the <a href="<?php echo $create_thesis_uri; ?>">Create new PhD Thesis</a>
            page in the held admin area. <br>
            Enter the title of the thesis into the designated input field and a short summary of the content into the text
            editor. <br>
            Use the box <strong>Thesis Details</strong> to enter additional information about the thesis. Fill the fields
            for the authors first and last name, The university and the faculty, which hosted the work, the publishing
            <em>year</em> the first and second assessor and optionally a link where the complete thesis can be viewed or
            downloaded.
        </p>
    </div>

    <div class="item-container">
        <h2><?php echo $count; $count++;?>. Managing Events</h2>

        <h3>How it works</h3>
        <p>
            The event posts on this website are automatically posted based on the Events on <em>various</em>
            <a>Indico</a> websites. <br>
            The website urls and the categories relevant for this website are hardcoded into the plugin source. So
            whenever the script to get the events is triggered all the events from these categories will be "downloaded.
            The script will then filter out what events are already as posts on the held website and only post the
            new ones.
        </p>

        <h3>Updating the events</h3>
        <p>
            To run the script, which will check and eventually post new events based on the indico sites, navigate
            to the <a href="<?php echo $commands_uri; ?>">Commands section</a>, select the command
            "start_fetch_indico_events" and press the button <strong>execute</strong>. <br>
            If you wish to watch the progress of the script go to the
            <a href="<?php echo $logs_uri; ?>">Log Posts</a>. The top most post should contain the Log of the script,
            that was just executed.
        </p>


        <h3>Using Indico for a single event</h3>
        <p>
            First, you'll need to visit the indico page you want to import the event
            from. At this moment the only options are <a href="https://indico.desy.de/indico">https://indico.desy.de/indico</a>
            and <a href="https://indico.scc.kit.edu/indico">https://indico.scc.kit.edu/indico</a>. <br>
            On the page navigate to the specific event and get the <strong>indico ID</strong>. The indico ID is the number
            that is being displayed in the URL of the event page.
        </p>
        <p>
            Go to the <a href="<?php echo $create_event_uri; ?>">Create new Event</a> tab on the held website. <br>
            Use the "Request Event from Indico" Box by selecting the site URL from which the event is and pasting the indico
            ID into the field beneath. <br>
            Press <strong>Publish</strong> and the Event is being imported
        </p>

        <h3>Doing it manually</h3>
        <p>
            Go to the <a href="<?php echo $create_event_uri; ?>">Create new Event</a> tab on the held website. <br>
            TODO.
        </p>
    </div>

    <div class="item-container">
        <h2><?php echo $count; $count++;?>. Manage Publications</h2>

        <h3>How it works</h3>
        <p>
            The publication posts on this site are automatically generated from the scopus database using a script.
            On this site are multiple profiles for different scientist/authors of scientific papers. These profiles
            contain simple information like the name, but most importantly a <strong>scopus ID</strong>. <br>
            Using this scopus ID of each author the script can gather <em>all</em> the publications of all the authors
            for which a profile exists. <br>
            The script then compares which of these publications already exist as posts on the held website and only
            keeps the new ones. These are then additionally filtered (for example by publishing date and affiliation,
            collaborations etc.). <br>
            The publications which are left over after this last step will be added as new posts to the website.
        </p>

        <h3>Updating publications</h3>
        <p>
            To run the script, which will check for new publications, navigate to the
            <a href="<?php echo $commands_uri; ?>">Command section</a> and select the Command
            <strong>start_fetch_scopus_publications</strong> and press the button execute. <br>
            If you wish to watch the progress of the script go to the
            <a href="<?php echo $logs_uri; ?>">Log Posts</a>. The top most post should contain the Log of the script,
            that was just executed.
        </p>
        <p>
            It should be noted, that publications, that are not clearly excluded from the website by the affiliation
            blacklist filter or the date filter will be posted as drafts. This means a manual selection of the new
            drafts will be necessary. <br>
            The automatic detection of collaboration papers is also very basic and will most certainly not work for all
            papers. The remaining collaboration papers will have to be tagged manually.
        </p>

        <h3>Tagging Collaborations</h3>
        <p>
            If there is a publication post, that is supposed to be a collaboration, but is not tagged as such, the tag
            has to be added manually. <br>
            Navigate to the post in question and use the right hand box <strong>Collaboration</strong>. Enter the name
            of the collaboration into the text field. <strong>Make sure the name is actually exactly the same (
            capitalizing!) as other papers tagged with this collaboration</strong>
        </p>

        <h3>Complementing KITOpen Links to existing publications</h3>
        <p>
            To run the script which will create additional KITOpen references to the publications, navigate to the
            <a href="<?php echo $commands_uri; ?>">Command section</a>, select the command
            <strong>start_complement_kitopen</strong> and press the execute button. <br>
            If you wish to watch the progress of the script go to the
            <a href="<?php echo $logs_uri; ?>">Log Posts</a>. The top most post should contain the Log of the script,
            that was just executed.
        </p>
    </div>



</div>
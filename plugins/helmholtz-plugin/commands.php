<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 17.07.18
 * Time: 11:48
 */
use the16thpythonist\Command\Command;
use Scopus\ScopusApi;
use the16thpythonist\Checker\HeuristicCollaborationGuesser;
use the16thpythonist\Indico\IndicoApi;
use the16thpythonist\Wordpress\Scopus\AuthorObservatory;


/**
 * Class GuessCollaborationsCommand
 *
 * This saves the
 */
class GuessCollaborationsCommand extends Command {

    /**
     * CHANGELOG
     *
     * Added 17.07.2018
     *
     * @param array $args
     * @return void
     */
    protected function run(array $args)
    {
        $this->log->info('Creating the query for all the posts with "ANY" collaboration status');
        $args = array(
            'post_type'         => 'post',
            'post_status'       => 'any',
            'posts_per_page'    => -1,
            'tax_query'         => array(
                array(
                    'taxonomy'      => 'collaboration',
                    'field'         => 'slug',
                    'terms'         => array('any'),
                    'operator'      => 'IN',
                )
            ),
        );
        $query = new WP_Query($args);
        $posts = $query->get_posts();
        $this->log->info('Publications with that status: ' . count($posts));
        $scopus_api = new ScopusApi(API_KEY);
        $publications = array_map(function($i) use ($scopus_api) {return new Publication($scopus_api, $i->ID);}, $posts);


        $collaborations = array(
            'CMS',
            'AUGER',
            'KATRIN',
            'ALICE',
            'ATLAS'
        );
        $guesser = new HeuristicCollaborationGuesser($collaborations);
        /* @var $publication Publication */
        foreach ($publications as $publication) {
            $title = $publication->title;
            $tags = $publication->tags;

            $guesser->set($title, $tags);
            if ($guesser->hasGuess()) {
                $guess = $guesser->guess;
                wp_set_object_terms($publication->id, array($guess), 'collaboration', false);
                global $wpdb;
                $where = array('ID' => $publication->id);
                $args = array(
                    'post_status'   => 'publish',
                    'post_date'     => $publication->published,
                );
                $wpdb->update($wpdb->posts, $args, $where);
                $this->log->info('GUESSED "' . $guess . '" FOR: '. hh_post_link($publication->id));
            } else {
                $this->log->info('NO COLLABORATION FOR: ' . hh_post_link($publication->id));
            }
        }
    }
}
//GuessCollaborationsCommand::register('guess_collaborations');


/**
 * Class AuthorMetricsCommand
 *
 * CHANGELOG
 *
 * Added 18.09.2018
 *
 * Deprecated 19.11.2018
 * Rewrote the whole thing in a separate package
 *
 * @deprecated
 */
class AuthorMetricsCommand extends Command {

    protected function run(array $args)
    {
        $this->log->info('Starting to update the author metrics');
        list($author_nodes, $author_links) = hh_author_metrics();

        $this->log->info('These are the results:');
        $this->log->info('The amount of publications per author');
        foreach ($author_nodes as $author => $count) {
            $this->log->info($author . ': ' . $count);
        }
        $this->log->info('The non-zero-weight links between the authors (author collaborations) amongst all publication posts');
        foreach ($author_links as $link) {
            if ($link['weight'] !== 0) {
                $this->log->info($link['source'] . ', ' . $link['target'] . ' : ' . $link['weight']);
            }
        }
    }
}
// AuthorMetricsCommand::register('update_author_metrics');

/**
 * Class ScopusFetchCommand
 *
 * A command, that will fetch new publications from the scopus database and posts them on the website
 *
 * CHANGELOG
 *
 * Added 08.08.2018 - 0.0.1.14
 *
 * Changed 09.08.2018 - 0.0.1.15
 * Set the max author count from 25 to 50, because there was the possibility of some authors not being included into
 * the system (thus not represented properly by the author metrics) due to their name being at the end of the alphabet
 * and the publication just having a lot of authors, even though not being a collaboration.
 *
 * Deprecated 19.11.2018
 * Rewrote the whole thing in its own package
 *
 * @deprecated
 *
 * @since 0.0.1.14
 */
class ScopusFetchCommand extends Command {

    const START_DATE = '2014-01-01';
    const MAX_AUTHOR_COUNT = 25;

    public $api;
    /** @var AuthorObservatory $author_observatory */
    public $author_observatory;

    public $current_scopus_id;
    public $current_post_id;

    /**
     * The custom functionality that gets called, when the command is executed
     *
     * CHANGELOG
     *
     * Added 08.08.2018
     *
     * @since 0.0.1.14
     *
     * @param array $args
     *
     * @return void
     */
    protected function run(array $args)
    {
        $this->fetch();
    }

    /**
     * CHANGELOG
     *
     * Added 05.06.2018
     *
     * Changed 10.06.2018
     * Added the additional parameter 'comment_status' => 'closed' to the wp insert statement
     *
     * Changed 17.06.2018
     * Added a line, that will remove all the duplicate entries from the final array of the new scopus ids. This is
     * important because duplicates will exist whenever two or more authors have worked on the same publication.
     *
     * Changed 26.06.2018
     * Changed the logging facility from "ScopusLog" to "LogPost" and added a few additional log messages
     *
     * Changed 28.06.2018
     * Added a START_YEAR constant to the class, which specifies the eraliest date to accept publications from all
     * publications older will be dismissed
     *
     * Changed 03.07.2018
     * After changing the author observatory checkPublication function to also indicate when a publication is
     * blacklisted, the publications that are blacklisted are now actually dismissed from the fetch, not even drafted.
     *
     * Changed 08.08.2018
     * Moved the whole functionality over to the new "Command" system.
     * Removed the session object and all the calls to the session.
     *
     * @since 0.0.1.14
     */
    private function fetch()
    {
        $this->log->info("Creating Scopus Api & Author Observatory");

        $this->api = new ScopusApi(API_KEY);
        $this->author_observatory = new AuthorObservatory();

        $old_scopus_ids = $this->loadOldScopusIds();
        $this->log->info(sprintf('Total of "%d" scopus ids already in the system', count($old_scopus_ids)));
        $fetched_scopus_ids = $this->author_observatory->fetchScopusIDs();
        $this->log->info(sprintf('Total of "%d" scopus ids fetched', count($fetched_scopus_ids)));

        $scopus_ids = array_diff($fetched_scopus_ids, $old_scopus_ids);
        $this->log->info(sprintf('Scopus ids to be loaded, after removing duplicates "%d"', count($scopus_ids)));

        $dismiss_counter = 0;
        $load_counter = 0;

        foreach ($scopus_ids as $scopus_id) {
            try{

                $abstract = $this->api->retrieveAbstract($scopus_id);
                $coredata = $abstract->getCoredata();

                $title = $coredata->getTitle();
                $date = $coredata->getCoverDate();

                // Checking if the date of the publication is not lower than the start date
                if (strtotime($date) - strtotime(self::START_DATE) <= 0) {
                    $this->log->info(sprintf('DISMISSED "%s" was too old with date "%s"', $title, $date));
                    $dismiss_counter += 1;
                    continue;
                }

                // The check method returns -1 for blacklist, 1 for publish and 0 for draft (quarantine)
                $publication_check = $this->author_observatory->checkPublication($abstract);
                // Checking for blacklist
                if($publication_check < 0) {
                    $this->log->info(sprintf('BLACKLIST "%s"', $title));
                    $dismiss_counter += 1;
                    continue;
                }

                $post_status = ($publication_check > 0 ? 'publish': 'draft');

                $this->current_scopus_id = $scopus_id;

                $tags = $this->getTags($abstract);
                $eid = $this->getAbtractEid($abstract);

                $args = array(
                    'post_type' => 'post',
                    'post_author' => 5,
                    'post_date' => $coredata->getCoverDate(),
                    'post_status' => $post_status,
                    'post_content' => sanitize_textarea_field($coredata->getDescription()),
                    'post_title' => sanitize_text_field($coredata->getTitle()),
                    'comment_status' => 'closed',
                    'meta_input' => array(
                        'scopus_id' => $coredata->getScopusId(),
                        'volume' => $coredata->getVolume(),
                        'doi' => $coredata->getDoi(),
                        'published' => $coredata->getCoverDate(),
                        'eid' => $eid,
                    ),
                    'tax_input' => array(
                        'journal' => sanitize_text_field($coredata->getPublicationName()),
                        'post_tag' => $tags,
                        'author' => array(),
                        'collaboration' => "NONE",
                    )
                );

                $post_id = wp_insert_post($args);
                $this->current_post_id = $post_id;
                $this->addAuthors($abstract);
                $this->addCategories($abstract);

                $this->log->info(sprintf('%s publication "%s"', strtoupper($post_status), hh_post_link($post_id)));
                $load_counter += 1;
            } catch (Exception $e) {
                $dismiss_counter += 1;
                $this->log->error(sprintf('ERROR for scopus ID "%s"', $scopus_id));
            }
        }

        $this->log->info(sprintf('Loaded "%d" Dismissed "%d"', $load_counter, $dismiss_counter));
    }

    /**
     * Loads a list of all the scopus Ids of the publications already posted on the website
     *
     * CHANGELOG
     *
     * Changed 11.06.2018
     * Added an additional argument 'post_type' => 'any' to the parameters of the get_posts call, so that even the
     * drafts are being loaded
     *
     * Changed 08.08.2018
     * Moved the whole functionality over to the new "Command" system
     *
     * @return array
     */
    private function loadOldScopusIds() {
        // Getting all the publication posts from the api
        $args = array(
            'numberposts' => -1,
            'post_type' => 'post',
            'post_status' => 'any'
        );
        $publication_posts = get_posts($args);
        $old_scopus_ids = array();
        foreach ($publication_posts as $post) {
            $post_id = $post->ID;
            $scopus_id = get_post_meta($post_id, 'scopus_id', true);
            $old_scopus_ids[] = $scopus_id;
        }
        return $old_scopus_ids;
    }

    /**
     * Returns the EID string for the given abstract
     *
     * CHANGELOG
     *
     * Changed 08.08.2018
     * Moved the whole functionality over to the new "Command" system
     *
     * @since 0.0.1.14
     *
     * @param \Scopus\Response\Abstracts $abstract
     * @return string
     */
    private function getAbtractEid(\Scopus\Response\Abstracts $abstract) {
        $coredata = $abstract->getCoredata();
        // This is a hack to access protected fields in PHP. This is needed because there is no public getter function
        // for the eid
        $closure = function () { return $this->data; };
        $data = Closure::bind($closure, $coredata, \Scopus\Response\AbstractCoredata::class)();
        if ( array_key_exists('eid', $data) ){
            return $data['eid'];
        } else {
            return '';
        }
    }

    /**
     * Returns the list of tags for the given abstract object
     *
     * CHANGELOG
     *
     * @since 0.0.1.14
     *
     * @param \Scopus\Response\Abstracts $abstract
     * @return array
     */
    private function getTags(\Scopus\Response\Abstracts $abstract) {
        $closure = function() { return $this->data; };
        $data = Closure::bind($closure, $abstract, \Scopus\Response\Abstracts::class)();

        try {
            if ( array_key_exists('idxterms', $data) ) {
                $mainterm = $data['idxterms']['mainterm'];
                $tags = array();
                foreach ( $mainterm as $entry ){
                    $tag = $entry['$'];
                    $tags[] = $tag;
                }
                return $tags;
            } else {
                return array();
            }
        } catch (Exception $e) {
            $this->log->error('THERE WAS A PROBLEM WITH GETTING THE TAGS OF ' . $this->current_scopus_id);
            return array();
        }
    }

    /**
     * @param \Scopus\Response\Abstracts $abstract
     * @return mixed
     */
    private function getCategories(\Scopus\Response\Abstracts $abstract) {
        return $this->author_observatory->getCategoriesPublication($abstract);
    }

    /**
     * Given the abstract object of the publication, the authors defined in there will be added to the current post as
     * terms of the "author" taxonomy. Because all the authors would be to much for collaboration publications for
     * example only as many authors as specified in the MAX_AUTHOR_COUNT field will be added.
     *
     * CHANGELOG
     *
     * Changed 11.06.2018
     * Added an incrementing statement at the end of each iteration for the count variable, so that the count break
     * condition gets met at some point.
     *
     * Changed 08.08.2018 - 0.0.1.14
     * Moved the whole functionality over to the new "Command" system
     *
     * Changed 09.08.2018 - 0.0.1.15
     * Used 'self::MAX_AUTHOR_COUNT' instead of 'this->', because I introduced the maximum amount of authors as a class
     * constant not as a field. This should fic the problem of every publication only having one author term.
     *
     * @since 0.0.1.14
     *
     * @param \Scopus\Response\Abstracts $abstract
     */
    private function addAuthors(\Scopus\Response\Abstracts $abstract) {
        $authors = $abstract->getAuthors();
        $count = 0;
        foreach ($authors as $author) {
            if ($count <= self::MAX_AUTHOR_COUNT) {
                $author_name = $author->getIndexedName();
                $author_id = $author->getId();

                $result = term_exists($author_id, 'author');
                if (empty($result)) {
                    wp_insert_term(
                        $author_name,
                        'author',
                        array( 'slug' => $author_id )
                    );
                }

                wp_set_post_terms($this->current_post_id, $author_name, 'author', true);
            } else {
                break;
            }
            $count += 1;
        }
    }

    /**
     * CHANGELOG
     *
     * Changed 26.06.2018
     * Now additionally adds the category "Publications" to each and every post, because that is needed to properly
     * display the posts in the front end of the website
     *
     * Changed 08.08.2018
     * Moved the whole functionality to the new "Command" system
     *
     * @since 0.0.1.14
     *
     * @param $abstract
     */
    private function addCategories($abstract) {
        $categories = $this->getCategories($abstract);
        $categories[] = 'Publications';
        wp_set_object_terms($this->current_post_id, $categories, 'category');
    }
}
// ScopusFetchCommand::register('fetch_scopus_publications');


/**
 * Class IndicoFetchCommand
 *
 * A command, that will fetch new events from indico and posts those, that have not been already posted
 *
 * CHANGELOG
 *
 * Added 08.08.2018
 *
 * @deprecated
 *
 * @since 0.0.1.14
 */
class IndicoFetchCommand extends Command {

    public $current_api;
    public $current_post_id;

    /**
     * The code that is actually executed when the command is called
     *
     * CHANGELOG
     *
     * Added 08.08.2018
     *
     * @since 0.0.1.14
     *
     * @param array $args
     *
     * @return void
     */
    protected function run(array $args)
    {
        $this->fetch();
    }

    /**
     * Fetches all the events from indico and posts those, that are not already posted
     *
     * CHANGELOG
     *
     * Added 08.08.2018
     *
     * @since 0.0.1.14
     */
    private function fetch() {
        $loaded_indico_ids = $this->loadedIndicoIds();
        $this->log->info(sprintf('Events already in the system: "%d"', count($loaded_indico_ids)));

        $dismiss_counter = 0;
        $load_counter = 0;
        foreach (INDICO_CATEGORIES as $url=>$info) {
            try{
                $key = $info['key'];
                $category_ids = $info['categories'];

                // Creating a new Api object
                $this->log->info(sprintf('Creating a new API object for "%s" with key "%s"', $url, $key));
                $this->current_api = new IndicoApi($url, $key);

                $this->log->debug(var_export($category_ids, true));
                foreach ($category_ids as $category_id) {

                    $this->log->info(sprintf('Starting to fetch for category "%s"', $category_id));
                    $events = $this->current_api->getCategory($category_id);
                    $this->log->info(sprintf('Fetched "%d" Events from the category "%s"', count($events), $category_id));

                    foreach ($events as $event) {
                        /** @var \the16thpythonist\Indico\Event $event */

                        // If the event is already in the system it is being dismissed
                        $id = $event->getID();
                        $title = $event->getTitle();
                        if (in_array($id, $loaded_indico_ids)) {
                            $this->log->info(sprintf('DISMISSED "%s" already in the system', $title));
                            $dismiss_counter += 1;
                            continue;
                        }

                        //$this->log->info(sprintf('Posting "%s"...', $title));

                        $args = array(
                            'post_type'         => 'event',
                            'post_author'       => 5,
                            'post_title'        => $title,
                            'post_content'      => $event->getDescription(),
                            'post_date'         => $event->getModificationTime()->format(HELMHOLTZ_DATE_FORMAT),
                            'post_status'       => 'publish',
                            'comment_status'    => 'closed',
                            'meta_input'        => array(
                                'indico_id'     => $event->getID(),
                                'url'           => $event->getURL(),
                                'start_date'    => $event->getStartTime()->format(HELMHOLTZ_DATETIME_FORMAT),
                                'end_date'      => $event->getEndTime()->format(HELMHOLTZ_DATETIME_FORMAT),
                            ),
                            'tax_input'         => array(
                                'type'          => $event->getType(),
                                'location'      => $event->getLocation(),
                                'creator'       => $event->getCreator()->getFullName(),
                            ),
                        );

                        $this->current_post_id = wp_insert_post($args);
                        $this->log->info(sprintf('PUBLISH "%s"', hh_post_link($this->current_post_id)));
                        $load_counter += 1;
                    }
                }

            } catch (Exception $e) {
                $this->log->error(sprintf('ERROR category "%s"', $url));
            }
        }
    }

    /**
     * Returns a list of the indico ids of all the events already posted on the website
     *
     * CHANGELOG
     *
     * Added 08.08.2018
     *
     * @since 0.0.1.14
     *
     * @return array
     */
    private function loadedIndicoIds() {
        // Getting all the event post objects already posted on the website, using a Wordpress Query
        $args = array(
            'post_type'         => 'event',
            'post_status'       => 'any',
            'posts_per_page'    => -1
        );
        $query = new WP_Query($args);
        $event_posts = $query->get_posts();

        // Creating a list with only the indico ids. The function calls a post meta retrieval for the indico id given
        // the event Post object
        $indico_ids = array_map(function ($e){return get_post_meta($e->ID, 'indico_id', true);}, $event_posts);
        return $indico_ids;
    }
}
//IndicoFetchCommand::register('fetch_indico_events');
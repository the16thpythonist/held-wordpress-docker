<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 26.06.18
 * Time: 09:10
 */
use Log\LogPost;
use Log\LogInterface;
use Log\VoidLog;
use the16thpythonist\Checker\SimpleCollaborationGuesser;
use the16thpythonist\KITOpen\KITOpenApi;
use the16thpythonist\Indico\IndicoApi;
use the16thpythonist\Wordpress\Data\DataPost;

$VOID_LOG = new VoidLog();


/**
 * CHANGELOG
 *
 * Added 26.06.2018
 *
 * @since 0.0.6.10
 *
 * @param string $category
 */
function hh_add_global_category(string $category) {
    $log = new LogPost();
    $log->start();
    $log->info('Adding the category "' . $category . '" to each post');
    // THis function will add a certain category to all the posts
    $args = array(
        'numberposts'       => -1,
        'post_type'         => 'post',
        'post_status'       => 'any',
    );
    $posts = get_posts($args);
    $log->info('Processing ' . count($posts) . ' posts in total');
    foreach ($posts as $post) {
        $post_id = $post->ID;
        wp_set_object_terms($post_id, array($category), 'category', true);
        $log->info('Added category to post ' . $post_id);
    }
    $log->stop();
}


function hh_check_collaborations() {
    $log = new LogPost();
    $log->start();

    $collaborations = array(
        'CMS'       => 'CMS',
        'AUGER'     => 'AUGER',
        'KATRIN'    => 'KATRIN'
    );
    $guesser = new SimpleCollaborationGuesser($collaborations, 20);

    $args = array(
        'numberposts'       => -1,
        'post_type'         => 'post',
        'post_status'       => 'any'
    );
    $scopus_api = new \Scopus\ScopusApi(API_KEY);
    $posts = get_posts($args);
    $log->info('Checking collaborations. For a total of ' . count($posts) . ' posts');
    foreach ($posts as $post) {
        $publication = new Publication($scopus_api, $post->ID);
        $args = array(
            'tags'      => $publication->tags,
            'title'     => $publication->title,
            'authors'   => $publication->authors
        );
        $title_linked = '<a href="' . get_the_permalink($post->ID) . '">' . $post->post_title . '</a>';
        $guesser->set($args);
        if ($guesser->suspectsCollaboration()) {
            $args = array(
                'ID' => $post->ID,
                'post_status' => 'draft',
            );
            wp_update_post($args);
            wp_set_post_terms($post->ID, 'ANY', 'collaboration', true);
            $log->info('ANY "' . $title_linked . '"');
        } else {
            wp_set_post_terms($post->ID, 'NONE', 'collaboration', true);
            $log->info('NONE "' . $title_linked . '"');
        }
    }
}


/**
 * Gets all the posts, that were posted before the given year
 *
 * CHANGELOG
 *
 * Added 27.06.2018
 *
 * Changed 28.06.2018
 * Changed the argument 'numberposts' to 'posts_per_page' because the former only works with get_posts and the latter
 * actually works with the WP_Query object used
 *
 * @since 0.0.1.9
 *
 * @param int $year             the year, which is the limit, before which all posts are being returned
 * @param string $post_type     OPTIONAL the post type whose posts to return. Default is the publication type 'post'
 * @return array
 */
function hh_posts_before_year($year, string $post_type='post') {
    $args = array(
        'post_type'         => $post_type,
        'post_status'       => array('draft', 'publish'),
        'posts_per_page'    => -1,
        'date_query'        => array(
            array(
                'before'    => array(
                    'year'  => $year
                )
            )
        )
    );
    $query = new WP_Query($args);
    $posts = $query->get_posts();
    return $posts;
}


/**
 * puts the given string into a hyperlink tag, that links to the post page of the given post id
 *
 * CHANGELOG
 *
 * Added 27.06.2018
 *
 * @since 0.0.1.8
 *
 * @param int $post_id      the id of the post to whose page the string should be linking
 * @param string $string    the string supposed to act as the link to the post page
 *
 * @return string
 */
function hh_link_post($post_id, string $string) {
    $permalink = get_the_permalink($post_id);
    $string_linked = '<a href="' . $permalink . '">' . $string . '</a>';
    return $string_linked;
}


/**
 * Gets an author wrapper object if given the scopus author id of that author
 *
 * CHANGELOG
 *
 * Added 28.06.2018
 *
 * Deprecated 20.11.2018
 *
 * @deprecated
 *
 * @since 0.0.1.9
 *
 * @param int $author_id    the scopus author id of the author post to get.
 * @param null $scopus_api  OPTIONAL if a scopus api object already exists in the outer scope, where this function is
 *                          being called, then it can be passed and no new one has to be created in the function
 *
 * @throws InvalidArgumentException if the website does not contain a post to the given author id
 *
 * @return Author the author wrapper
 */
function hh_get_author($author_id, $scopus_api=NULL) {
    // Getting the author for the author id
    $args = array(
        'post_type'     => 'author',
        'post_status'   => 'any',
        'meta_query'    => array(
            array(
                'key'       => 'scopus_author_id',
                'value'     => $author_id,
                'compare'   => 'IN'
            )
        )
    );
    $query = new WP_Query($args);
    $posts = $query->get_posts();
    if (count($posts) == 0) {
        throw new InvalidArgumentException('There is no author with the id ' . $author_id . ' on the website');
    } else {
        /*
         * The scopus api object is needed for the author wrapper object, but only creating a new api object if none has
         * been passed through the arguments. This is mainly for efficiency reasons, in a big loop you dont want to
         * create a new API object for every new author wrapper you want to create...
         */
        $scopus_api = ($scopus_api == NULL ? new \Scopus\ScopusApi(API_KEY) : $scopus_api);
        $post_id = $posts[0]->ID;
        $author = new Author($scopus_api, $post_id);
        return $author;
    }
}


/**
 * Calls the KIT Open API and returns an array of publication objects for the given search args
 *
 * CHANGELOG
 *
 * Added 10.07.2018
 *
 * @since 0.0.1.10
 *
 * @see KITOpenApi
 *
 * @param array $args   The array of the arguments for the KITOpen API call
 * @param bool $assoc   if this is false a normal, indexed array with all the publications will be returned. If this
 *                      is True however an associative array will be returned, which has the publication DOI as keys
 *                      ans the publication objects as values
 * @return array
 */
function hh_kit_open_publications(array $args, bool $assoc=False): array {
    $api = new KITOpenApi();
    $publications = $api->search($args);

    if ($assoc === True) {
        /* @var $publication \the16thpythonist\KITOpen\Publication*/
        $assoc_array = array();
        foreach ($publications as $publication) {
            $doi = $publication->getDOI();
            if ($doi !== '') {
                $assoc_array[$doi] = $publication;
            }
        }
        return $assoc_array;
    } else {
        return $publications;
    }
}


/**
 * Adds KIT Open ids to all the posts, where a Kit open entry exists
 *
 * CHANGELOG
 *
 * Added 10.07.2018
 *
 * Changed 10.07.2018 - 0.0.1.11
 * Replaced the adding of the meta key with an update, so that consecutive runs of this function dont mess up the
 * publications, where an id was already added.
 *
 * Changed 16.07.2018 - 0.0.1.11
 * Stopped using the 'IPE' institute filter to get all the publications, using a filter that concatenates all the author
 * names with a 'or', so that publications, that are not directly affiliated with the IPE can also get considered.
 * Also using all the types now, to decrease the chance of missing out on one.
 *
 * Changed 15.08.2018
 * Added the parameter 'log' to the function. The log object now has to be passed in from the outside scope. This makes
 * it possible to use this function directly as a command (because with commands the log already exists).
 * The Default is a void log object, which will log nothing.
 *
 * Changed 15.08.2018
 * Using the upper string versions of the DOI for the comparison now, as that prevents mistakes in the DOI formatting
 * affecting the results.
 *
 * Changed 16.08.2018
 * The log parameter is now NULL on default and a new VoidLog is being created if the parameter is null.
 * Had to change this because a constant cannot evaluate to a object
 *
 * Deprecated 20.11.2018
 *
 * @deprecated
 *
 * @param LogInterface $log The log to use
 *
 * @since 0.0.1.10
 */
function hh_complement_kit_open(LogInterface $log=NULL) {
    if ($log === NULL) {
        $log = new VoidLog();
    }

    $authors = hh_all_authors();
    $author_names = array();
    /* @var $author Author */
    foreach ($authors as $author) {
        $name = $author->last_name . ', ' . $author->first_name[0] . '*';
        $author_names[] = $name;
    }
    $author_query = implode(' or ', $author_names);
    $args = array(
        'year'      => '2012-',
        'type'      => 'BUCHAUFSATZ,BUCH,HOCHSCHULSCHRIFT,ZEITSCHRIFTENAUFSATZ,ZEITSCHRIFTENBAND,PROCEEDINGSBEITRAG,PROCEEDINGSBAND,FORSCHUNGSBERICHT,VORTRAG,POSTER,FORSCHUNGSDATEN,REZENSION_BUCH,REZENSION_ZEITSCHRIFT,LEXIKONARTIKEL,MULTIMEDIA,SONSTIGES',
        'author'    => $author_query
    );
    $publications = hh_kit_open_publications($args, True);
    $log->info('Total fetches from KIT Open API: ' . count($publications));
    // var_dump(count($publications));wp_die();
    // Getting all the publication posts
    $args = array(
        'post_type'         => 'post',
        'post_status'       => 'publish',
        'posts_per_page'    => -1,
    );
    $query = new WP_Query($args);
    $posts = $query->get_posts();

    /* @var $publication \the16thpythonist\KITOpen\Publication */
    /* @var $post WP_Post */
    $publication_dois = array_map(function ($s){return strtoupper($s);}, array_keys($publications));
    //$log->info(var_export($publication_dois, true));
    foreach ($posts as $post) {

        $doi = get_post_meta($post->ID, 'doi', True);
        if (in_array(strtoupper($doi), $publication_dois)) {
            $publication = $publications[$doi];
            $kit_open_id = $publication->getID();
            update_post_meta($post->ID, 'kitopen', $kit_open_id);
            $log->info('Post "' . hh_post_link($post->ID) . "' Added KIT Open ID: " . $kit_open_id);
        }
    }
}


/**
 * Requests a single event object from a indico site, given the url and the event id
 *
 * The url has to be one of the urls, the wordpress site supports, because only for those an api key is available,
 * which is necessary for making the api call.
 *
 * CHANGELOG
 *
 * Added 16.07.2018
 *
 * @since 0.0.1.11
 *
 * @throws InvalidArgumentException if the given url is not found in the array of supported indico sites
 *
 * @param string $url
 * @param string $id
 * @return \the16thpythonist\Indico\Event
 */
function hh_indico_single_event(string $url, string $id) {
    /*
     * All the observed indico sites (which means all the sites for which keys exist) are saved in an array. The keys
     * of this array are the urls to the sites. So if the given url doesnt match with those sites, no api key is
     * available, thus no request can be made
     */
    $indico_sites = array_keys(INDICO_CATEGORIES);
    if (in_array($url, $indico_sites)) {
        $key = INDICO_CATEGORIES[$url]['key'];
        $api = new IndicoApi($url, $key);
        $event = $api->getEvent($id);
        return $event;
    } else {
        throw new InvalidArgumentException('The given url is not part of the accepted indico sites');
    }
}


/**
 * Creates a string with a html link tag to the given post, using its title
 *
 * CHANGELOG
 *
 * Added 16.07.2018
 *
 * @since 0.0.1.11
 *
 * @param string $post_id
 * @return string
 */
function hh_post_link(string $post_id) {
    $title = get_the_title($post_id);
    $uri = get_the_permalink($post_id);

    $link = '<a href="'. $uri . '">' . $title . '</a>';
    return $link;
}

/**
 * CHANGELOG
 *
 * Added 09.08.2018
 *
 * Deprecated 20.11.2018
 *
 * @deprecated
 *
 * @return array
 */
function hh_all_authors() {
    $args = array(
        'post_type'         => 'author',
        'post_status'       => 'any',
        'posts_per_page'    => -1,
    );
    $query = new WP_Query($args);
    $posts = $query->get_posts();

    $scopus_api = new \Scopus\ScopusApi(API_KEY);
    $create_author = function($item) use ($scopus_api) {return new Author($scopus_api, $item->ID); };
    return array_map($create_author, $posts);
}


/**
 * Returns an array of the publication wrapper objects to all the publications in the system
 *
 * CHANGELOG
 *
 * Changed 09.08.2018
 * The collaborations were being included when the collaborations flag was false, due to a false negating statement.
 * Also when drafts was true it would only get the drafts, now it will also get the drafts (any post status)
 *
 * Deprecated 20.11.2018
 *
 * @deprecated
 *
 * @since 0.0.1.13
 *
 * @param bool $drafts          flag, whether to also include drafts. Default: False
 * @param bool $collaborations  flag, whether to include collaboration papers. Default: False
 * @return array
 */
function hh_all_publications(bool $drafts=False, bool $collaborations=False) {
    $collaborations_query = array(
        'taxonomy'      => 'collaboration',
        'field'         => 'slug',
        'terms'         => array('none'),
        'operator'      => 'IN'
    );
    $args = array(
        'post_type'         => 'post',
        'post_status'       => ($drafts ? 'any' : 'publish'),
        'posts_per_page'    => -1,
    );
    if ($collaborations) {
        $args['tax_query'] = $collaborations_query;
    }

    $query = new WP_Query($args);
    $posts = $query->get_posts();

    $scopus_api = new \Scopus\ScopusApi(API_KEY);
    $create_publication = function($item) use ($scopus_api) {return new Publication($scopus_api, $item->ID); };
    return array_map($create_publication, $posts);
}


/**
 * CHANGELOG
 *
 * Added 16.07.2018 - 0.0.1.11
 *
 * Deprecated 20.11.2018
 *
 * @deprecated
 *
 * @since 0.0.1.11
 */
function hh_author_metrics() {

    $authors = hh_all_authors();
    /* @var $author Author */
    $get_name = function($author) {return $author->last_name . ' ' . substr($author->first_name, 0, 1) . '.'; };
    $author_assoc = array();
    foreach ($authors as $author) {
        $name = $get_name($author);
        $author_assoc[$name] = $author;
    }
    $author_names = array_keys($author_assoc);
    $author_indices = array();
    foreach ($author_names as $index => $author_name) {
        $author_indices[$author_name] = $index;
    }
    $publications = hh_all_publications(True, False);

    $author_counts = array();
    foreach ($author_names as $author_name) {
        $author_counts[$author_name] = 0;
    }

    $get_pairs = function(array $author_names) {
        $author_names = array_values($author_names);
        $pairs = array();
        $max = count($author_names) - 1;
        foreach (range(0, $max - 1) as $i) {
            foreach (range($i + 1, $max) as $j) {
                $authors = array($author_names[$i], $author_names[$j]);
                sort($authors);
                $pairs[] = implode(';', $authors);
            }
        }
        return $pairs;
    };

    $author_collabs = array();
    $pairs = $get_pairs($author_names);

    foreach ($pairs as $pair) {
        $author_collabs[$pair] = 0;
    }

    /* @var $publication Publication */
    foreach ($publications as $publication) {
        /** @var $a Author */
        // Get all the author terms for a single publication and then filter based on the author ids
        $_author_ids = array_map(function($a){return $a->getId();}, array_values($author_assoc));
        $_author_terms = wp_get_post_terms($publication->id, 'author');
        $_authors = array();
        echo '<br>';
        foreach ($_author_terms as $term) {
            /** @var WP_Term $term */
            if (in_array($term->slug, $_author_ids) || in_array($term->name, $author_names)) {
                $_authors[] = explode('.', $term->name)[0] . '.';
            }
        }
        var_export($_authors);
        //$_intersect = array_intersect($_authors, $author_names);
        foreach ($_authors as $_author) {
            $author_counts[$_author] += 1;
            //$author_nodes[$_author] += 1;
        }
        //wp_die(var_dump($_intersect));
        if (count($_authors) >= 2) {
            $pairs = $get_pairs($_authors);
            foreach ($pairs as $pair) {
                $author_collabs[$pair] = $author_collabs[$pair] + 1;
            }
        }
    }
    arsort($author_collabs);
    hh_save_json('author-collaboration-count', $author_collabs);
    hh_save_json('author-publication-count', $author_counts);
    $author_links = array();
    $max_weight = max($author_collabs);
    // Creating the links array from the assoc collab array
    foreach ($author_collabs as $pair_string => $count) {
        if ($count !== 0) {
            list($author1, $author2) = explode(';', $pair_string);
            $author_links[] = array(
                'source'        => $author_indices[$author1],
                'target'        => $author_indices[$author2],
                'weight'        => $count * (1 / ($max_weight * 4))
            );
        }
    }

    $author_nodes = array();
    foreach ($author_counts as $author => $count) {
        $weight = ceil(3*log($count + 2));
        $author_nodes[] = array(
            'label'         => $author,
            //'weight'        => $weight,
            'radius'        => $weight,
            'index'         => $author_indices[$author]
        );
    }
    hh_save_json('author-nodes', $author_nodes);
    hh_save_json('author-links', $author_links);
    return array($author_nodes, $author_links);
}


/**
 * Wraps all the WP_Post objects given in an array with the Publication wrapper.
 *
 * CHANGELOG
 *
 * Added 17.07.2018
 *
 * Deprecated 20.11.2018
 *
 * @deprecated
 *
 * @since 0.0.1.12
 *
 * @param array $posts  the array of WP_Post objects to be wrapped with the Publication wrapper class
 * @return array
 */
function hh_publications_from_posts(array $posts) {
    $scopus_api = new \Scopus\ScopusApi(API_KEY);
    $get_publication = function (WP_Post $post) use ($scopus_api) {
        return new Publication($scopus_api, $post->ID);
    };
    return array_map($get_publication, $posts);
}


/**
 * Saves the given data as JSON file
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 *
 * Changed 29.08.2018
 * Using the package 'wp-data-safe' the JSON data is now being saved in wordpress posts and not files.
 *
 * @since 0.0.1.13
 *
 * @param string $name
 * @param array $data
 */
function hh_save_json(string $name, array $data) {
    $file = DataPost::create($name . '.json');
    $file->save($data);
}


/**
 * Loads data structure from a JSON file
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 *
 * Changed 29.08.201
 * Using the package 'wp-data-safe' the JSON data is now being saved in wordpress posts and not files.
 *
 * @since 0.0.1.13
 *
 * @param string $name
 * @return array|mixed|object
 */
function hh_load_json(string $name) {
    $file = DataPost::load($name . '.json');
    return $file->load();
}
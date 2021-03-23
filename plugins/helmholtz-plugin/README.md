# Helmholtz detectors plugin

This is the child theme for the helmholtz, Matter Technologies, Detector Systems 
website, short MTDTS

## Testing

So it is possible to have unit tests for wordpress. That means with a framework, that completely loads the wordpress 
environment so plugin functions can be tested properly. This possibility is provided by 
[WP-CLI](https://make.wordpress.org/cli/handbook/plugin-unit-tests/).

Here is how to run the test suite:

**1)** Make sure, that "wget" is installed on your system, by using the following command for example

```bash
$ whereis wget
```

If no path is shown as the output, it is most likely not installed.

**2)** Create a new test database for wordpress. called for example *wp_test*

**3)** Execute the script *install-wp-tests.sh* in the *bin* folder within the helmholtz plugin folder. The script 
expects the following arguments: The name of the database to use for the tests (in this case "wp_test"), the username 
of the database, the password for the database, the host of the database and the wordpress version to be used.

```bash
$ cd /var/www/html/project/wp-content/plugins/helmholtz/bin
$ bash install-wp-tests.sh wp_test username password latest
```

The script will install a test environment to the /tmp folder of the machine. Thus this folder will be deleted with 
every reboot!

**4)** Goto the helmholtz folder and run phpunit. It will automatically discover all unit tests, that are stored within 
the helmholtz folder and its sub folders.

```bash
$ cd /vaw/www/html/project/wp-content/plugins/helmholtz
$ phpunit
```

# Changelog

### 0.0.0 - 10.04.2018

- Initial version

### 0.0.0.1 - 22.05.2018

After a little testing this is the first functioning version.\
The helmholtz plugin is a site specific plugin for the Helholtz detectors systems website:
- The standard "post" post type has been rewritten as the "publication" post type, which represents a 
publication to be featured on the website. Some important publication properties have been mapped to the 
post attributes: The post content for example will be used for the description/abstract of the paper. 
publication meta data such as the DOI, the publication date and the volume have been added as custom post 
meta fields. The author research field affiliations have been mapped to be represented by the native post 
categories, the publication tags are used as post tags, the authors, the journal and the collaboration have 
been added as custom tag-like taxonimies.
- The scopus requests to the scopus database have been fully implemented using php. After accessing the 
additional scopus control panel in the admin section of the wordpress page, a button can be pressed to trigger 
a background process, that will request all the publications from the specified authors and add them as a new 
post. The admin panel even features statistics about the database request including the amount of fetched 
publications and the estimated remaining time for the process, updated live, via AJAX

**important notice: The php timeout has to be adjusted for correct functionality**, 
this is done by using adding the line *"set_time_limit(60 * 60 * 12);"* somewhere within 
the *wp-config.php* file

Known issues:\
- The scopus API was changed, which is why the citations to a given publication cannot be fetched anymore...

Future goals:\
- The blacklist/whitelist system is not yet implemented in the process of fetching the publications from the authors
- The authors, for which to fetch all the publications still have to be hardcoded into an array within the plugins 
source code. The authors can also be modeled with a custom post type.
    - within those custom author posts there could be an ajax call to the scopus website to view all their 
    affiliations etc. interactively. Adding removing whitelist blacklist via a checkbox possibly
- Adding filters: Maybe when one only wants to get from a certain date?

### 0.0.0.2 - 25.05.2018

Added:
- The Author custom post type. In the main admin menu there is a new option to create a "Author" post. These 
posts are the models for the authors, which are supposed to be used as the basis for the publications to be put on 
the website.\
When the publication fetching is being started all those posts will be loaded and based on their meta data the list of 
publications to fetch will be assembled. Also these posts are used to assign the special author categories to the 
Publications, after they have been posted
    - There is a meta box in the edit section of the author post, that can be used to save the relevant innformation 
    about the author: The first and last name, the scopus author id (at the moment singular) A comma separated list of 
    the whitelist and blacklist affiliation ids and the category (also singular at the moment). 
    - A php script also calls the scopus api for all the affiliations of that author before displaying the page (may 
    cause a long loading time) and shows all the affiliation ids and their respective institution names on the edit page, 
    so that when creating a new post, one can see which affiliation id belongs to which institute and they can be added
    to the whitelist/blacklist accordingly.
    
Future goals:
- The mechanism for getting the author affiliations:
    - It is done by server side scripting, which means the page will have to be reloaded once the author id has been 
    typed in, this should be done via javascript and be dynamic, which means as soon as a valid author id was typed in 
    they start to show up
    - A better way of typing in the white/blacklist would be by ticking a checkbox behind the displayed 
    affilition name 
- The fetching of the publications:
    - The scopus white/blacklist behaviour is not implemented yet, which has to be done 

### 0.0.0.3 - 25.05.2018

Changed:
- The publication fetch mechanism:
    - After the publications are fetched, a check method of the author observatory will be called on the 
    retrieved Abstracts object. This method returns the boolean value of whether or not the publication is supposed to 
    be posted onto the website or not based on the affiliations of the authors of the publication compared with the 
    black and whitelists of the observed authors. At the moment is posted ONLY when the affiliation of the author in the 
    given Abstract is in the whitelist, otherwise it will be dismissed.
    
Future goals:
- The publication fetch mechanism:
    - It would be better if only the publications with an explicit blacklisting will be dismissed and the other 
    publications, that have neither a blacklist nor a whitelist rule will be posted as drafts to wordpress, so that a 
    admin can post/dismiss them manually.
    
### 0.0.1.0 - 02.05.2018

This is the version, that introduces the indico event functionality.

Added:
- The event custom post type: Each event fetched from an "indico" website will be modeled as a post of the type 'event',
where the title is mapped to the post title, the description being mapped to the post content. The start date, end date,
address, url to the source and id are saved as additional post meta data to the post. The location, creator and type of 
the event are represented as taxonomy terms for the post, so that they can be grouped and searched by. Additionally the 
publish date of the post is mapped to the date of last modification within the indico platform.
- The indico api: A very basic indico API object has been created to enable fetching the indico data directly from php.
In this point in time, the api only supports basic retrieval by category and events are only as detailed as the 
start, end and mod time, the creator and basic meta like title, description, location type etc.
- The indico fetch mechanism: The indico fetch has also been implemented as a wordpress background task. All the events 
of the specified websites and categories (at the moment given by a const. array) are fetched and only those, that are 
not already on the website will be posted additionally.

Changed:
- The scopus options page on the admin dashboard of the website has been extended with a indico section 
from there on a new indico fetch session can be started, by clicking a button and the progress will be viewed by repeatedly 
fetching the json data about the current indico session and displaying them in the section.

Future goals:
- Improve the indico fetch mechanism, so that once the modification date on a fetched event is different from the 
modification date of the already posted version, this version will be replaced with the newer one.


### 0.0.1.1 - 03.06.2018

Changed:
- The fetch rate for the AJAX call to get the scopus session info to be displayed in the scopus options page in the 
admin dashboard was lowered from 1s to 400ms
- Added a try-catch in the "Author" wrapper class for fetching the affiliations, since there was a problem, where an 
empty author id (which means when attempting to add a new author post) would cause an excpetion and thus hang the whole 
page

### 0.0.1.2 - 05.06.2018

Added:
- The ScopusSession class, which manages access to the scopus session file (contains the information about a current 
scopus fetch process, running in the background).

### 0.0.1.3 - 11.06.2018

Changed:
- Added an extra argument during the creation of a new post by the scopus fetch process, which disables all the comments
on the post
- The scopus options page class for all the span's that display the scopus session data was renamed from 
'scopus-statistics' to 'scopus-session'
- The scopus option page ids for all the span's that display the scopus session data have been renamed to match the 
name of the keys of the json data structure carrying the new session data
- The scopus fetch process now drafts publications, that are not whitelisted
- Fixed a bug with the new scopus fetch request not working correctly 
- The journal string is now also being loaded into the attributes of the Publication wrapper objects

Added:
- The file '/cpt/thesis.php': Registers a new post type especially designed to input and represent posts about student 
contributions in the form of a PhD Thesis. Also contains the code for adding a custom meta box with all the thesis 
specific information and how to save this data as post meta fields.
    - it is planned to use the 'cpt' folder for files, where each file will bundle all the functions needed to create a 
    new custom post type
    
### 0.0.1.4 - 12.06.2018

Added:
- A 'Thesis' wrapper object in the file '/cpt/thesis.php' so that the theme can access the custom meta data more easily 
when using them to display the post page.
- File 'css/editor.css': The stylesheet which will contain all the styles needed for the wordpress dashboard post edit 
screen, for example additional custom meta boxes.
- File 'pages/thesis_meta_box.php': The template file for the content of the meta box used in the thesis editor, to 
input all the thesis specific data

Changed:
- Fixed and issued with the attributes of the publication wrapper object not being loaded properly, when there didnt 
exist any terms for a given taxenomy

### 0.0.1.5 - 18.06.2018

Changed:
- Moved all the functions for modifying the standard 'post' post type to a separate file 'cpt/post.php' and only loading 
that file in the main plugin file now.
- Moved the functions for the 'event' post type to the separate file 'cpt/event.php'
- Began to move the functions for the 'author' post type to the file 'cpt/author.php'
- Moved all the ajax related functions into the new file 'ajax.php' in the plugin root, which is included into the main 
file

Added:
- There was an issue when posted as a draft and then published the post publish date would be the current date. Added a 
hook on the saving process, that always changes the publish date of the post to be the publish date of the publication.
- Created the new taxonomy 'selection' for the 'post'(publication) type. This can be used to mark certain publications, 
with a special tag and this tag can be used to list all the marked publications with the shortcode 
'display-recent-publications' and the corresponding 'selection' parameter.
- Extended the helmholtz options screen in the admon dashboard with an additional widget 'selection-search', which 
enables the quick adding of a selection term to a publication post. The name of the post is being typed into the search
field and an AJAX request continously sends this search request to the server, which then returns the best fitting 
results. The first and thus best matching result is always the selected one for adding the term. The term can be written 
in another input form and upon the click of a button this term is being added as a "selection" tax term to the 
first publication in the search response

### 0.0.1.6 - 19.06.2018

Added:
- A new file "cpt/highlight.php" with the new "Highlight" post type
    - Added a new custom taxonomy 'category' to the highlight type, which can be used to tag certain posts with terms, 
    by which the output of the shortcode to display all the highlights can be filtered
    
### 0.0.1.6 - 19.06.2018

- Moved the whole shortcode functionality from the theme to the plugin
    - Added the folder 'templates/shortcodes', that contains all the template files for the shortcodes
    - Added the folder 'shortcodes.php', which contains all the functions to add the shortcodes to wordpress and which 
    is being included from within the main plugin file
 
## 0.0.1.7 - 26.06.2018

- Added the package ['wp-pi-logging'](https://github.com/the16thpythonist/wp-pi-logging) to the list of 
dependencies for the project. The package offers wordpress logging facilities, at the moment only logging to be 
saved as a post type, using the 'LogPost' class. 
- Changed the indico fetch process to utilize the 'LogPost' logging facility
- Changed the scopus fetch process to utilize the 'LogPost' logging facility
- Fixed the bug, where the Category taxonomy for the highlights sort of deleted the categories 
for the publications
- Added file 'functions.php' which will contain all the utility functions for the project, that cannot be 
assigned a clear module to resign in
- Added function 'hh_add_global_category' to easily add a specific category to each and every post in the 
system right now.
- Modified the scopus fetch process, so that it now adds the category 'Publications' to every publication post, because 
that is needed to properly display all the categories on the front end.

### 0.0.1.8 - 27.06.2018

- Added the function 'hh_link_post', which turns a string into a link to a post page, if given the string and the 
post id to which to link.
- Changed the logging for the collaboration checking process so that the post titles in the log now actually link to the 
actual post page.
- Changed the logging fot the scopus fetch session so that the titles of the posts now actually link to the post in 
question.

### 0.0.1.9 - 28.06.2018

- Added the function 'hh_posts_before_year', which returns all the posts before a given year
- Added a style rule to hide the search widget on the primary sidebar when in desktop mode but 
display it in mobile mode, when the search bar in the header isnt visible anymore.
- Added more descriptive dashicons for all the custom post types
- Added the function 'hh_get_author', which queries for a post based on the scopus author id of an author 
and then returns the Author wrapper object to that post if the specified post existed
- Changed the author post meta box in a way, that affiliation whitelist and blacklist are no longer 
being managed via the a list and csv input into a text field, but an actual widget, where blacklist and 
whitelist can be ticked in a checkbox right besides the name
    - Changed the save process of author post types
    - Changed the author meta box template
    - Changed the options.css for correct layout of the widget
- Added the 'functions.js' script file, which will contain more generalized javascript functionality 
for the helmholtz plugin
    - Added the function 'getAuthorAffiliations', which returns the affiliation info for an author, given 
    his scopus author id via AJAX call to the server.
- Added the 'admin.js' script file
    - Contains the author affiliation widget javascript, planning on migrating the whole admin js code there 
    soon
    
### 0.0.1.10 10.07.2018

- Fixed issue with 'hh_posts_before_year' not returning all posts, but only 10 at a time
- Changed the scopus fetch process, so that blacklist publications are now actually dismissed not even 
drafted
- Added the function 'hh_kit_open_publications', which wraps the KIT Open api and returns the search results to
the given search args.
- Added the function 'hh_complement_kit_open', which adds kit open Ids to all those publication post, that have a
kit open entry.
- Added a method 'isKITOpen' to the Publication wrapper class, which checks if the publication has 
a KITOpen id assigned to it
- Added a method 'getKITOpenURL' to the Publication wrapper class, which returns the url to the 
specific KITOpen page, if it has a KITOpen id.

### 0.0.1.11 - 16.07.2018

- Added the package ['indico-api'](https://github.com/the16thpythonist/indico-api) to the dependencies
- Added a meta box to the event post type, which will let the user create a new event post from requesting 
a single indico event from one of the observed websites.
- Added a function which will create html link strings to a post given its post id

### 0.0.1.12 - 19.07.2018

- Added the package ['wp-commands'](https://github.com/the16thpythonist/wp_commands.git) to the project. This package 
adds a new class Template "Command", which can be used to create background commands functionality. It also adds a new 
menu page on the admin dashboard. On this menu page all the registered commands can be selected and remotely called. 
The output of the commands is then saved into a logging LogPost post.
- created the new file "commands.php" in the main folder of the project. It will contain all the "Command" subclasses, 
that specifiy some sort of background task.
- Added the command "GuessCollaborationsCommand", which will make a simple guess as to which collaboration a 
publication belongs to and then post it based on that guess.
- Added function "hh_publications_from_posts": Wraps all the WP_Post objects given in an array with 
Publication wrappers.
- Added the shortcode 'list-categories', which will display a listing (ul) of all the publication categories and the 
amount of posts for each.
    - added the function 'hh_shortcode_list_categories' ind the 'shortcodes.php' file
    - Added the template file 'templates/shortcodes/list_categories.php'
- Added the shortcode 'list-categories', which will display a listing (ul) of all the collaborations and the 
amount of posts assigned to each.
    - Added the function 'hh_shortcode_list_collaborations' in the 'shortcodes.php' file
    - Added the template file 'templates/shortcodes/list_collaborations.php'

### 0.0.1.13 - 20.07.2018

- Added the file 'defines.php': Moved all the constant definitions from the main helmholtz file there
- Added the 'assets' folder, which will contain JSON files and images
- Changed the 'update_author_metrics' function, so that the format of the generated arrays matches the 
format needed by the d3.js visualisation framework
- Added a JS file, that creates a force layout of the author collaboration metrics.
- Added 'AuthorMetricsCommand', which can be used to trigger a metrics recalculation from the wordpress backend

### 0.0.1.14 - 08.08.2018

- Fixed the error, where the author metrics svg wasnt being displayed for unregistered visitors of the site
(In the future remember to add "wp_ajax_nopriv" hook).
- Added 'IndicoFetchCommand': A command class that will fetch new events from the indico sites and post those that 
are not already posted
- Added 'ScopusFetchCommand': A command class that will fetch the publications based on the observed authors from 
scopus data base and post those that are not already posted
- Removed the original implementations of indico and scopus fetch in 'helmholtz.php'
- The session model is no longer being used for the fetch processes
    - Removed class 'ScopusSession' from 'helmholtz.php'
    - Removed class 'IndicoSession' from 'helmholtz.php'
    - Removed class 'Session' from 'helmholtz.php'
    - Removed the file 'indico_session.json'
    - Removed the file 'scopus_session.json'
    - Removed the function 'updateScopus' from 'options.js'
    - Removed the function 'updateIndico' from 'options.js'
    - Removed the function 'startIndico' from 'options.js'
    - Removed the function 'startScopus' from 'options.js'
- Removed 'ScopusLog' class from the 'helmholtz.php': Logging is now being done by a separate module
    - Removed the file 'scopus_session.log'
    
### 0.0.1.15 - 15.08.2018

- Fixed the issue with the ScopusFetchCommand only adding one author term to each publication
- Increased the max author count for the fetch process
- defines.php: Added a "HELMHOLTZ_PLUGIN_WIDGET_TEMPLATES_PATH" of the path, that contains the PHP/HTML templates of the 
  custom widgets.
- Added 'widgets.php'
    - Added a dashboard widget, which will display a tutorial for the helmholtz plugin functionality
- Added '/templates/widgets/dashboard_tutorial.php': Contains the html code of the Tutorial that is being displayed on 
the admin dashboard, explaining the Helmholtz plugin functionality.
- Created a tutorial widget, that is being displayed in the admin dashboard. It currently explains how to 
Add a new highlight, a new phd Thesis, a new event and how to execute the scripts to get new events from indico or new 
publications from scopus.
- Created a Command for complementing publication posts with additional KITOpen links

### 0.0.1.16 - 11.09.2018

- Fixed the warning about constants not being able to evaluate to objects by setting the default of the 
log parameter of the function 'hh_complement_kitopen' to NULL.
- Extended the tutorial by a section, that explains how to update the kit open links.
- Added the package [wp-data-safe](https://github.com/the16thpythonist/wp-data-safe.git): The package introduces 
a new custom post type, which can be used to store different data structure in wordpress posts using a file like 
naming system. This package was added due to this website being hosted on a openshift server. in this environment, 
saving a file to a folder inside of one virtual maschine, running the server, will not be translated to the other 
VM's, if the folder in question was not specially marked in openshift. To avoid issues, generic data files can now 
just be saved as wordpress posts, who are always persistent among instances.
- Added the package [wp-scopus](https://github.com/the16thpythonist/wp-scopus.git): At this point in time it only 
supports the AuthorPost, which is a object orientented refactoring of the author post type used on this website, with 
a few additional features.

### 0.0.2.0 - 20.11.2018

- Rewrote the whole Scopus functionality into a separate package [wp-scopus](https://github.com/the16thpythonist/wp-scopus.git)
    - Removed all the old code for scopus functionality
    
    
### 0.0.2.1 - 05.12.2018

- Minor Bug fix, which fixed the drag and drop issues in the wordpress admin dashboard

### 0.0.2.2 - 07.01.2018

- Removed all functionality for Indico events from the helmholtz package
- Now using the indico functionality as a separate package [wp-indico](https://github.com/the16thpythonist/wp-indico.git), 
which has better stability and upgraded features.

### 0.0.3 - 15.10.2019

- Fixed the CSS of the navbar to be displayed correctly again
- Updated version of the scopus-wp package, which has an updated front end interface for specifying author information 
input

### 0.0.4 - 18.10.2019

- Added a first version of the manual PDF

### 0.0.5 - 03.12.2019

- Updated the "wp-scopus" plugin to version 0.0.4
- Updated the "wp-indico" plugin to version 0.0.2
- Disabled comments by CSS (simply making the field for the input invisible)

### 0.0.6 - 17.12.2019

- Updated the "thesis" custom post type
    - The original wrapper class "Thesis" is deprecated and being replaced by "ThesisPost",
    as this new class fits in better with the established standard of the "wp-scopus" and "wp-indico"
    packages
    - The registration of this post type is also no longer done by individual functions, 
    but uses the "ThesisPostRegistration" class for that now
    - The Thesis post type now has a custom admin list view, which additionally displays the author 
    of the thesis as well as the department
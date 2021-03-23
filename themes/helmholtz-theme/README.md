# Description

This is the child theme for the helmholtz, Matter Technologies, Detector Systems 
website, short MTDTS


# Changelog

### 0.0.0 - 10.04.2018

- Initial version

### 0.0.1 - 10.04.2018

- Fixed the issue, that the prev and next links on the bottom of a singular page 
not working

### 0.0.2 - 22.04.2018

- Fixed the issue that when narrowing the window size and the sidebar being attached to the first 
column to be displayed underneath the actual post content, the container for the sidebar would not 
display correctly.
- Changed the linear gradient design of the carousel caption to be a single background color with 
the helmholtz blue and a little bit opacity
- Added a if condition, that does not add a margin to the main content body in case the window width 
is in mobile size
- Added a php library to the project, which allows to specifiy required plugins for the theme and 
also created the function for all these requirements in the functions.php file.

### 0.0.3 - 24.04.2018

- Changed the mobile view to now incorporate the logo instead of the title
- Fixed the drop down button for the navigation bar in mobile view

### 0.0.4 - 24.04.2018

- Fixed the logo being way to big for a phone screen by adding a new conditional css size step at
approximately the size of a phone screen with a smaller version of the whole header design

### 0.0.5 - 02.05.2018

- Changed the project structure
    - Renamed the "scripts" folder to "js"
    - Created a "css" folder
    - Created a "fonts" folder and moved all the font files from the main project folder there
- Added to functions.php: functions which are used to enqueue css stylesheets and javascripts files into the 
html header using the wordpress queue, instead of hardcoding it into the html head.

### 0.0.5.1 - 02.05.2018

- Updated the README file

### 0.0.5.2 - 02.05.2018

- Added a "contact" shortcode. Using this shortcode a contact name, role and email can be specified together with 
a portrait of the person to create a special layout with the contact info being displayed at the bottom 
right border of the picture when the shortcode is used

### 0.0.5.2 - 06.05.2018

- Moved the whole code from the main theme style.css into a separate style.css file because this way
it prevents browsers from using the cache. only left the needed comment spec in the style.css to identify the theme
- Removed the top padding between the header nav menu and the main content area in mobile few of the website

### 0.0.5.3 - 07.05.2018

- Fixed the bug with the whole site layout being blown off, because the stylesheets did not enqueue in the 
correct order

### 0.0.5.5 - 07.05.2018

- Fixed the bug, which made it possible to scroll into nothingness horizontally when viewing on a mobile device

### 0.0.5.7 - 09.05.2018

- Added an additional "institute" attribute to the contact shortcode, which will display the university and or institute 
of the contact right beneath the name of the contact

### 0.0.6.0 - 03.06.2018

Added:
- The 'single-event.php' file, which will be used as the template for displaying a post of the custom post type 
'event', that has been introduced by the helmholtz plugin v0.0.0.4 and is a model of the information fetched from 
various "indico" site APIs
- The shortcode 'events', which will echo a list of event posts in the order of their starting date

Changed:
- The 'singular.php' file. With the introduction of the helmholtz plugin, the standard 'post' post type is now a data 
model for the 'publication' type. Which means, that most of the data belonging to a publication such as the doi, the 
authors etc. are no longer part of the actual post content body, but are now saved as separate meta values and taxonomies. 
The singular template has been updated to display the same content as before, being used with the new post data structure.
 
### 0.0.6.1 - 03.06.2018

Changed:
- Renamed the modified 'singular.php' to 'single-post.php', so that the template is only being used for the actual 
posts. Before there was a problem with the static pages, which are also being templated by the 'singular.php' file
 
ToDo

- Resizing the window: Generally a bad idea to resize the window with changing the width of the 
elements via java script, better to change the properties like margin etc dynamically...
- Make the font size of the body smaller, when the media size gets smaller as well
- The mobile version is supposed to look like the mobile version of the bootstrap layout
- The Contact
    - A contact shortcode that generates the html
    - The css for the contact
    - test
    
### 0.0.6.2 - 11.06.2018

Added:
- functions, which check if the helmholtz plugin is installed or not and then disable the new 'single-post.php' template 
for posts, which will only work with the new custom modifications of the helmholtz plugin. This way for 
the singular.php will also be used for post types on old systems

Changed:
- Additional if condition in the archive.php, that checks if the plugin is available, before using any 
plugin specific functions and classes

### 0.0.6.3 - 11.06.2018

Added:
- A new element in the 'single-post.php' template, which will display the journal in which a publication was 
published, given there is one to be displayed (non empty string retrieved from post tax. data)

### 0.0.6.4 - 12.06.2018

- Tweaked the optics of the new journal element of the post template. The actual journal name is now in cursive and not
the 'in'
- Added the file 'single-thesis.php', which is the content template for the 'Thesis' post type 
introduced in the helmholtz plugin
- Added the file 'shortcodes.php', which is supposed to become the file, where all shortcodes are being defined. Added 
the shortcodes 'display-recent-publications', 'display-recent-theses' and 'list-events'.
- Added the file '/templates/shortcodes/display_recent_publications.php': The Template file for a shortcode, that will 
output a list with the most recently published publications.
- Added the file '/templates/shortcodes/display_recent_theses.php': The Template file for a shortcode that will output 
a list with the most recent, finished thesis posts
- Added the file '/templates/shortcodes/list_events.php': The Template file for a shortcode that will output a listing 
of the next upcoming events

### 0.0.6.5 - 18.06.2018

- Added the file '/templates/shortcodes/display_selected_publications': The template file for the 
'display-selected-publications' shortcode, which will, if given the name of a selection term list all the publications 
that have been tagged with this specific term.
- Moved all the remaining shortcode functions from the main 'functions.php' file to the 'shortcodes.php' file and 
commented them.

### 0.0.6.6 - 18.06.2018

- Changed the (ul) and (li) tags in the shortcode templates "list_events.php", "display_recent_theses.php" and 
"display_recent_publications.php" to (div) tags, as that is the way it is in the live site as well

### 0.0.6.7 - 19.06.2018

- style.css: For mobile version:
    - decreased the padding of the main text body to the border of the screen 
    - decreased font size of the page title
    - decreased font size of the menu items for the main navigational menu
    - increased font size for normal text
    - decreased line height for normal text
    
### 0.0.6.8 - 19.06.2018

- Added the file 'templates/shortcodes/display_recent_highlights.php': Contains the template for creating a listing of 
the most recent highlights added to the page. The shortcodes features a limit to how many highlights are supposed to be 
displayed and a filter by what category has been added to the highlights.

### 0.0.6.9 - 19.06.2018

- Moved the whole shortcode functionality to the plugin, thus removed it from the theme

### 0.0.6.10 - 26.06.2018

- Slightly adjusted the design for a singular thesis display page in 'single-thesis.php': The first and 
second assessor arent in one line anymore, in fact they are in separate lines and surrounded by a one line margin 
top and bottom
- Slightly adjusted the design for displaying an 'archive.php' page: Added the light grey design of the author names and 
the journal beneath the title of the publication, but only if the plugin is available to support backwards compatibility
- Added the file 'search.php', which will act as the template to display search results. it filters out any display of 
a static page and the 'author' post type and will display each other CPT individually.

### 0.0.6.11 - 29.06.2018

- Adjusted the height of the image carousel banner in mobile mode to match the font and image size
- Added a mobile styling rule for the search widget in the sidebar

### 0.0.6.12 - 10.07.2018

- Changed the publication template 'single-post.php': Added another button at the end of the page wich will 
link to the kit open page of a publication, if one exists. If there is no kit open page, the button will not appear
    - Added style rules to align the buttons side by side
    
### 0.0.6.13 - 20.11.2018

- Changed the publication template to use the new PublicationPost objects, defined by the new wp-scopus package in 
the plugin

### 0.0.6.14 - 05.12.2018

- Changed the search template to use the new PublicationPost type

### 0.0.6.15 - 13.01.2018

- Changed the archive template to use the new PublicationPost type

### 0.0.6.16 - 03.10.2019

- Fixed the header navbar having the wrong flex configuration

### 0.0.7 - 15.12.2019

- Fixed the "Publications" site not working due to the deprecated function "get_field" within archives.php
- Adjusted the styles for the tags within a publication post page to now also be orange

### 0.0.8 - 17.12.2019

- Updated the theme details for the "thesis" custom post type
    - Updated the theme to be using the "ThesisPost" wrapper instead of the deprecated "Thesis" wrapper
    from the helmholtz plugin
    - Updated the thesis template html and css

### 0.0.9 - 17.12.2019

- Updated the Javascript and CSS files to make the design responsive for tablet and mobile applications
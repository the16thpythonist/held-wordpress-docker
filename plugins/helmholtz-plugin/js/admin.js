/**
 * @since 0.0.1.9
 *
 * CHANGELOG
 *
 * Added 28.06.2018
 */

/* THE AUTHORS AUTHOR META BOX AJAX FUNCTIONALITY */
/**
 * gets the author id from the according text input. Only works on a author post page
 *
 * CHANGELOG
 *
 * Added 28.06.2018
 *
 * @since 0.0.1.9
 *
 * @returns string
 */
function getAuthorID() {
    var author_id_input = jQuery('input#scopus_author_id');
    return author_id_input.attr('value');
}


/**
 * creates the author affiliation input widget in the author post meta box
 *
 * CHANGELOG
 *
 * Added 28.06.2018
 *
 * @since 0.0.1.9
 *
 * @returns void
 */
function authorAffiliations() {
    // Getting the author id from the input first
    var author_id = getAuthorID();
    console.log(author_id);
    // Getting the affiliations of that author
    getAuthorAffiliations(author_id, function (affiliations) {
        var affiliation_wrapper = jQuery('div#affiliation-wrapper');
        // for each affiliation adding the according checkboxes
        var key, value, whitelist_checked, blacklist_checked;
        for  (key in affiliations) {
            value = affiliations[key];
            if (value['whitelist'] === true) { whitelist_checked = ' checked'; } else { whitelist_checked = ''; }
            if (value['blacklist'] === true) { blacklist_checked = ' checked'; } else { blacklist_checked = ''; }
            var checkbox_whitelist_string = '<input type="checkbox" name="whitelist-' + key + '" value="1"' + whitelist_checked +'>';
            var checkbox_blacklist_string = '<input type="checkbox" name="blacklist-' + key + '" value="1"' + blacklist_checked +'>';
            var description_string = '<p class="first">' + key + ': ' + value['name'] + '</p>';
            var html_string = '<div class="affiliation-row">' + description_string + checkbox_whitelist_string + checkbox_blacklist_string + '</div>';
            var row_element = jQuery(jQuery.parseHTML(html_string));
            row_element.appendTo(affiliation_wrapper);
        }
    });
}


// 05.12.2018
// Replaced the '$' with 'jQuery' to fix the Bug, where the admin dashboard drag and drop wouldnt work
jQuery(document).ready(function () {
    authorAffiliations();
    jQuery('.accordion-section').accordion({
        active: 2
    });
    // console.log("should be");
});
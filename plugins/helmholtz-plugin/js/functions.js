/**
 * This function takes the response string of an AJAX call and turns into a JSON parsed object.
 *
 * CHANGELOG
 *
 * Added 14.06.2018
 *
 * Changed 17.06.2018
 * Replaced the explicit slice operation on the response string, which was there to remove the ominous '0' string at the
 * end of each AJAX response from wordpress with the function call to 'cleanResponse' A function, which does just that:
 * remove the last character of a string
 *
 * @param response string: The response string of an AJAX call
 */
function cleanJSON(response) {
    return JSON.parse(cleanResponse(response));
}


/**
 * The return of an Ajax call always comes back with a string '0' concat. at the end of the actual expected response.
 * This function removes the last character from a string
 *
 * @param response string: The response string of an AJAX call
 */
function cleanResponse(response) {
    return response.slice(0, -1);
}

/* THE AUTHOR META BOX AJAX FUNCTIONALITY */

/**
 * Requests the affiliation data structure from the server
 *
 * Requests the information about the affiliations for a given author from the server. In case of success the returned
 * data structure will have the affiliation key as the array key and as value another assocative array, that contains
 * the name and whether it is whitelisted/blacklisted.
 *
 * Example:
 * {
 *      '62761':    {
 *          'name':         'KIT Campus north',
 *          'whitelist':    true,
 *          'blacklist':    false
 *      },
 *      '532839':   {
 *          'name':         'HS Offenburg',
 *          'whitelist':    false,
 *          'blacklist':    true
 *      }
 * }
 *
 * CHANGELOG
 *
 * Added 20.06.2018
 *
 * @since 0.0.1.9
 *
 * @param author_id     the scopus author id of the author for which to get the affiliations
 * @param callback      the function to be called, once the affiliation data has been retrieved. the function must
 *                      accept a single parameter and that is the affiliation json data array
 * @returns {{}}
 */
function getAuthorAffiliations(author_id, callback) {
    var affiliations = {};
    jQuery("s");
    jQuery.ajax({
        url:        ajaxurl,
        type:       'Get',
        timeout:    100000,
        dataType:   'html',
        async:      true,
        data:       {
            'action':   'author_affiliations',
            'author':   author_id
        },
        error:      function (response) {
            console.log('error with retrieving the author affiliations');
            console.log(response);
            affiliations = {};
        },
        success:    function(response) {
            affiliations = cleanJSON(response);
            callback(affiliations);
        }
    });
    return affiliations;
}


/**
 * Requests a JSON asset file from the hh plugin
 *
 * CHANGELOG
 *
 * Added 19.07.2018
 *
 * @since 0.0.1.13
 *
 * @param name
 * @return {*}
 */
function getHelmholtzJSON(name) {
    var data;
    var nocache = Date.now();
    console.log(nocache);
    jQuery.ajax({
        url:        url,
        type:       'Get',
        timeout:    1000,
        dataType:   'html',
        async:      false,
        data:       {
            'action':   'get_json',
            'file':     name,
            'nocache':  nocache
        },
        error:      function (response) {
            console.log('Couldnt fetch JSON file from HELMHOLTZ plugin');
            console.log(response);
        },
        success:    function (response) {
            console.log(response);
            data = cleanJSON(response);
        }
    });
    return data;
}

jQuery(document).ready(function () {

});
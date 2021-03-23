var started = false;


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

/**  THE WIDGET FOR ADDING SELECTED PUBLICATIONS  **/

function getSearchTerm() {
    var searchInput = jQuery('input#selection-search');
    return searchInput.text();
}


function sendSearchTerm() {
    var searchInput = jQuery('input#selection-search');
    var search_content = searchInput.attr('value');
    var json_array;
    //console.log(search_content);
    jQuery.ajax({
        url: ajaxurl,
        type: 'Get',
        timeout: 500,
        dataType: 'html',
        data: {
            'action': 'selection_search',
            'search': search_content
        },
        error: function () {

        },
        success: function (response) {
            //console.log(response);
            json_array = cleanJSON(response);
            //console.log(json_array);
            displayResults(json_array);
        }
    })
}


function sendResult() {
    var results_container = jQuery('div#selection-search-result-container');
    var result_input = jQuery('input#selection-result');
    var status_text = jQuery('p#selection-status');
    var result_content = result_input.attr('value');
    var post_id = results_container.children().first().attr('id');
    jQuery.ajax({
        url: ajaxurl,
        type: 'Get',
        timeout: 500,
        dataType: 'html',
        data: {
            'action': 'selection_update',
            'selection': result_content,
            'post': post_id
        },
        error: function () {

        },
        success: function (response) {
            console.log(response);
            status_text.text(cleanResponse(response));
        }
    })
}


function displayResults(json_array) {
    var results_container = jQuery('div#selection-search-result-container');
    results_container.empty();
    var key;
    for (key in json_array) {
        var item = '<p id="' + key + '">' + json_array[key] + '</p>';
        results_container.append(item);
    }
    var size = Object.keys(json_array).length;
    var i;
    for (i = 0; i < 6 - size; i++) {
        results_container.append('<p> --- </p>');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: jonas
 * Date: 08.05.18
 * Time: 12:46
 *
 * CHANGELOG
 *
 * Changed 10.06.2018
 * Changed the class on the spans, which display the actual session data from 'scopus-statistics' to 'scopus-session'
 * because that rather reflects, the scopus session naming convention in the server side php code.
 * Changed the ids on those spans in the way, that now an underscore '_' is used instead of '-' to delimit two words,
 * to emphasize, that those are actually variables, that are used to update the content dynamically. Those ids now
 * actually match the names of the fields in the json data of the new session info being transmitted
 *
 * Changed 14.06.2018
 * Added a new section beneath the indico one, which contains a widget, that can be used for searching publications
 * and then adding selection terms for specific research areas to these publications quickly
 *
 * Changed 17.06.2018
 * Added another div to the selection-search widget, which will contain the text returned by the server on success.
 * This text will indicate, that the selection adding process was actually successfull and thus provide a little
 * feedback to the user of the widget.
 */


?>
<div id="scopus-page-container">
    <h1 id="scopus-options-title">
        Scopus
    </h1>
    <div id="scopus-form-container">
        <div id="scopus-interact-container">
            <div id="scopus-log-container">
                <div id="log">No log yet</div>
            </div>
            <div id="scopus-button-container">
                <p>Running: <span id="running" class="scopus-statistics">Not running...</span></p>
                <button id="start-scopus">start</button>
            </div>
        </div>
        <div class="helmholtz-options-statistics-container" id="scopus-statistics-container">
            <p>Starting Time: <span id="start_time" class="scopus-session"></span></p>
            <p>Time passed: <span id="total_time" class="scopus-session"></span></p>
            <p>End time: <span id="end_time" class="scopus-session"></span></p>
            <p>Publications fetched: <span id="fetch_amount" class="scopus-session"></span></p>
            <p>Total errors: <span id="error_amount" class="scopus-session"></span></p>
            <p>Fetches per minute: <span id="fetch_rate" class="scopus-session"></span></p>
            <p>Publications left: <span id="remaining_amount" class="scopus-session"></span></p>
            <p>Publications loaded: <span id="load_amount" class="scopus-session"></span></p>
        </div>
    </div>
    <h1 class="helmholtz-options-title">
        Indico
    </h1>
    <div class="helmholtz-options-form-container">
        <div class="helmholtz-options-interact-container" id="indico-options-interact-container">
            <div class="helmholtz-options-buttons-container">
                <p>Event active: <span id="running" class="indico-session"></span></p>
                <button id="start-indico">start</button>
            </div>
        </div>
        <div class="helmholtz-options-statistics-container" id="indico-options-statistics-container">
            <p>Start Time: <span id="start_time" class="indico-session"></span></p>
            <p>End Time: <span id="end_time" class="indico-session"></span></p>
            <p>Events fetched: <span id="fetch_amount" class="indico-session"></span></p>
            <p>Events updated: <span id="update_amount" class="indico-session"></span></p>
            <p>Events posted: <span id="post_amount" class="indico-session"></span></p>
            <p>Last Event ID: <span id="last_event_id" class="indico-session"></span></p>
        </div>
    </div>
</div>

<div id='selected-publications-widget'>
    <div id="selection-search-result-container">

    </div>
    <div>
        <input id="selection-search">
    </div>
    <div>
        <input id="selection-result">
        <button id="send-result">send</button>
    </div>
    <div>
        <p id="selection-status">None yet</p>
    </div>
</div>


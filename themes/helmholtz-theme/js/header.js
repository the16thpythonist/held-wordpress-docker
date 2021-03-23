
var sidebarFlag = true;

/**
 CHANGELOG

 Changed 22.04.2018
 I realized that the I mixed up in which function (vertical/horizontal) I had to detcach and attach to which column
 so i switched it around and it worked additionally with implementing that the flag actually gets inverted after the
 column has been reattached so that the whole process only gets executed once.
 Also had to change that the sidebar width is being set to the column size for the vertical display, because otherwise
 there would be a problem with that container not displaying correctly
 */
function sidebarVertical(window, row, column1, column2, sidebar) {
    var width = window.width();
    var newWidth;

    //row.width(width);
    //column2.width(0);
    if (width >= 766){
        newWidth = width - 30;
    } else {
        newWidth = width;
    }
    //sidebar.width(newWidth);
    //column1.width(newWidth);

    if (sidebarFlag) {
        console.log('Ataching the sidebar to the first column');
        column2.detach('.sidebar-wrap');
        sidebar.appendTo(column1);
        sidebarFlag = false;
    }
}

/**
CHANGELOG

 Changed 22.04.2018
 I realized that the I mixed up in which function (vertical/horizontal) I had to detcach and attach to which column
 so i switched it around and it worked additionally with implementing that the flag actually gets inverted after the
 column has been reattached so that the whole process only gets executed once.
*/
function sidebarHorizontal(window, row, column1, column2, sidebar){
    row.width(1170);
    sidebar.width(360);
    column2.width(360);
    column1.width(780);

    if (!sidebarFlag) {
        console.log('Attaching the sidebar to the second column');
        column1.detach('.sidebar-wrap');
        sidebar.appendTo(column2);
        sidebarFlag = true;
    }
}

var Icon = $('<li class="icon-wrapper"><div class="logo-wrapper-small"><div class="logo-letter-wrapper-small"><p><b>M</b></p></div><div class="logo-letter-wrapper-small" id="logo-second-letter-wrapper-small"><p class="logo-letter"><b>T</b></p></div><div class="logo-dts-wrapper-small"><p class=><span><b>D</b></span><b>TS</b></p></div><div class="after-logo-box-small"></div></div></li>');
Icon.css('opacity', 0);

var helmholtzFlag = true;
var searchbarFlag = true;

const maxScroll = 240;
const minWidth = 1170;
const mobileWidth = 770;



function updateNavigationScroll(window, menu, icon) {
    var width = window.width();

    // Only making the icon scroll out animation in case the window is not in mobile mode.
    if (width >= 1170) {
        var scroll = window.scrollTop();

        // Scrolling out the icon based on the scroll depth, where after the maximum scroll depth, the whole thing will
        // be held constant at the end position
        if (scroll <= maxScroll) {
            menu.css('margin-left', scroll * 0.58);
            icon.css('opacity', scroll * 0.0042);
        } else {
            menu.css('margin-left', 140);
            icon.css('opacity', 1);
        }
    }
}

const searchScroll = 160;

function updateSearchScroll(window, searchbar) {
    var width = window.width();

    // Only making the icon scroll out animation in case the window is not in mobile mode.
    if (width >= minWidth) {
        var scroll = window.scrollTop();

        if (scroll > searchScroll) {
            searchbar.css('margin-top', 15);
        } else {
            searchbar.css('margin-top', 35)
        }
    }
}

function updateResize(window, helmholtz, searchbar, row, column1, column2, sidebar) {
    var width = window.width();

    if (width <= minWidth) {
        // Remove the helmholtz bar from the page in case
        if (helmholtzFlag) {
            helmholtz.toggle();
            helmholtzFlag = false;
        }

        // Remove the widget area, where the search bar is in the header from the page
        if (searchbarFlag) {
            searchbar.toggle();
            searchbarFlag = false;
        }

        sidebarVertical(window, row, column1, column2, sidebar)

    } else {
        // Remove the helmholtz bar from the page in case
        if (!helmholtzFlag) {
            helmholtz.toggle();
            helmholtzFlag = true;
        }

        // Remove the widget area, where the search bar is in the header from the page
        if (!searchbarFlag) {
            searchbar.toggle();
            searchbarFlag = true;
        }

        sidebarHorizontal(window, row, column1, column2, sidebar)

    }
}


$(document).ready(function(){
    /* Hier der jQuery-Code */
    var menu = $('#menu-main');
    menu.prepend(Icon);

    var win = $(window);
    var helmholtzContainer = $('.helmholtz-img-container');

    var sidebar = $('.sidebar-wrap');
    var column1 = $('.col-sm-8');
    var column2 = $('.col-sm-4');

    var research = $('a[title*="Research"]');
    var about = $('a[title*="About"]');

    var searchbar = $('.widget-header');

    var row = $('.container');

    updateResize(win, helmholtzContainer, searchbar, row, column1, column2, sidebar);
    updateResize(win, helmholtzContainer, searchbar, row, column1, column2, sidebar);
    //updateResize(win, helmholtzContainer, searchbar, row, column1, column2, sidebar);

    win.bind('scroll', function () {
        updateNavigationScroll(win, menu, Icon);
        updateSearchScroll(win, searchbar);

    });

    win.bind('resize', function () {

        updateResize(win, helmholtzContainer, searchbar, row, column1, column2, sidebar);
    })
});
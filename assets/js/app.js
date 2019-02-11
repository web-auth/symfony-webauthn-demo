import '../css/app.scss';

import 'bootstrap-material-design/dist/js/bootstrap-material-design';

(function($) {
    'use strict'; // Start of use strict

    // Smooth scrolling using jQuery easing
    $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
        if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                $('html, body').animate({
                    scrollTop: (target.offset().top - 56)
                }, 1000, 'easeInOutExpo');
                return false;
            }
        }
    });

    // Closes responsive menu when a scroll trigger link is clicked
    $('.js-scroll-trigger').click(function() {
        $('.navbar-collapse').collapse('hide');
    });

    // Collapse Navbar
    var navbarCollapse = function() {
        var $nav = $('#mainNavbar');
        if ($nav.offset().top > 100) {
            $nav.addClass('navbar-shrink');
        } else {
            $nav.removeClass('navbar-shrink');
        }
    };

    // Collapse now if page is not at top
    navbarCollapse();

    // Collapse the navbar when page is scrolled
    $(window).scroll(navbarCollapse);

    var $body = $('body');

    $body.bootstrapMaterialDesign();
})(jQuery); // End of use strict

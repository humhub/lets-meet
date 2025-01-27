humhub.module('letsMeetWallEntry', function (module, require, $) {

    const init = function () {
        const width = $('.lets-meet-container .time-slot').outerWidth();

        $('.lets-meet-container .time-slot-vote').css('width', width + 'px');
    }

    const scrollLeft = function(event) {
        smoothScroll(event, '-');
    }
    const scrollRight = function(event) {
        smoothScroll(event, '+');
    }

    const smoothScroll = function(event, direction) {
        const container = event.$target.closest('.lets-meet-container');

        container.find('.scrollable-container').animate(
            {
                scrollLeft: direction + '=' + container.width() * 0.7
            },
            500
        );
    }

    module.export({
        init: init,
        scrollLeft: scrollLeft,
        scrollRight: scrollRight,
    });
});
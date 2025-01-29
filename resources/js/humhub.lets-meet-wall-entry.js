humhub.module('letsMeetWallEntry', function (module, require, $) {
    var Widget = require('ui.widget.Widget');
    var client = require('client');

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

    const vote = function(event) {
        const voteCell = event.$target.closest('.expanded-vote');
        const mainContainer = voteCell.closest('.lets-meet-container');
        const voteContainer = voteCell.closest('.votes-container');

        const alreadyVoted = event.$target.hasClass('voted');
        voteCell.find('.time-slot-vote').removeClass('voted');
        if (alreadyVoted) {
            event.$target.removeClass('voted');
            voteCell.removeClass('voted');
            voteCell.find('.vote-value').val('');
        } else {
            event.$target.addClass('voted');
            voteCell.addClass('voted');
            voteCell.find('.vote-value').val(event.$target.data('value'));
        }

        mainContainer.find(':submit').prop('disabled', voteContainer.find('.time-slot-vote.voted').length !== mainContainer.find('.expanded-vote').length)
    };

    const close = function(event) {
        client.post(event).then(function () {
            Widget.closest(event.$trigger).reload()
        }).catch(function (err) {
            module.log.error(err, true);
        });
    };

    module.export({
        scrollLeft: scrollLeft,
        scrollRight: scrollRight,
        vote: vote,
        close: close,
    });
});
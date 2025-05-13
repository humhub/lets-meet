/*!
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

humhub.module('letsMeet', function (module, require, $) {
    const Widget = require('ui.widget.Widget');
    const object = require('util.object');
    const client = require('client');
    const additions = require('ui.additions');
    const modal = require('ui.modal');
    const event = require('event');
    const status = require('ui.status');

    const WallEntry = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(WallEntry, Widget);

    WallEntry.prototype.init = function() {
        this.renderScrollButtons();
        this.listenResize();
        this.listenScrollWheel();
    }

    WallEntry.prototype.listenResize = function() {
        $(window).resize(this.renderScrollButtons.bind(this));
    }

    WallEntry.prototype.listenScrollWheel = function(event) {
        const scrollableContainer = this.$.find('.scrollable-container');
        const self = this;

        scrollableContainer.on('wheel', function(event) {
            if (event.originalEvent.deltaX !== 0) {
                event.preventDefault();
                scrollableContainer.scrollLeft($(this).scrollLeft() + event.originalEvent.deltaX);
                self.renderScrollButtons();
            }
        });
    }

    WallEntry.prototype.renderScrollButtons = function() {
        const scrollableContainer = this.$.find('.scrollable-container').first();
        const controlsContainer = this.$.find('.controls-container');
        const scrollLeftButton = this.$.find('.scroll-left');
        const scrollRightButton = this.$.find('.scroll-right');
        const scrollTolerance = 1;

        if (scrollableContainer.width() < scrollableContainer[0].scrollWidth) {
            controlsContainer.removeClass('d-none');
            scrollLeftButton.removeClass('d-none');
            scrollRightButton.removeClass('d-none');
        } else {
            scrollLeftButton.addClass('d-none');
            scrollRightButton.addClass('d-none');

            if (controlsContainer.find('.control-buttons').children().length === 0) {
                controlsContainer.addClass('d-none');
            }
        }

        scrollLeftButton.find('button').prop('disabled', scrollableContainer.scrollLeft() <= scrollTolerance);
        scrollRightButton.find('button').prop('disabled', scrollableContainer.scrollLeft() + scrollableContainer.innerWidth() >= scrollableContainer[0].scrollWidth - scrollTolerance);
    }

    WallEntry.prototype.scrollLeft = function(event) {
        this.smoothScroll(event, '-');
    }
    WallEntry.prototype.scrollRight = function(event) {
        this.smoothScroll(event, '+');
    }

    WallEntry.prototype.smoothScroll = function(event, direction) {
        this.$.find('.scrollable-container').animate(
            {
                scrollLeft: direction + '=' + this.$.width() * 0.8
            },
            300,
            this.renderScrollButtons.bind(this)
        );
    }

    WallEntry.prototype.vote = function(event) {
        const voteCell = event.$target.closest('.expanded-vote');
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

        this.$.find(':submit').prop('disabled', voteContainer.find('.time-slot-vote.voted').length !== this.$.find('.expanded-vote').length)
    };

    const Form = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(Form, Widget);

    Form.prototype.addDateRow = function (event) {
        event.preventDefault();

        const rowsContainer = this.$.find('#date-rows');
        const self = this;

        client.post(
            event,
            {dataType: 'html', data: {index: rowsContainer.find('.date-row').length}}
        ).then(function (response) {
            rowsContainer.append(response.response);
            additions.applyTo(rowsContainer);
            self.renderButtons();
        })
    }

    Form.prototype.removeDateRow = function(event) {
        event.$target.closest('.date-row').remove();
        this.renderButtons();
    }

    Form.prototype.renderButtons = function() {
        const rowsContainer = this.$.find('#date-rows');

        rowsContainer.find('.add-row').addClass('d-none');

        if (rowsContainer.find('.date-row').length === 1) {
            rowsContainer.find('.remove-row').addClass('d-none');
        } else {
            rowsContainer.find('.remove-row').removeClass('d-none');
        }

        rowsContainer.find('.add-row').last().removeClass('d-none');
    }

    Form.prototype.inviteAllMembers = function(event) {
        const newInvitesForm = this.$.find('#new-invites-form');
        const invitesList = this.$.find('.invites');

        if (event.$target.prop('checked')) {
            newInvitesForm.addClass('d-none');
            invitesList.addClass('d-none');
        } else {
            newInvitesForm.removeClass('d-none');
            invitesList.removeClass('d-none');
        }
    }

    Form.prototype.removeParticipant = function(event) {
        const participant = event.$target.closest('li');

        $('input[value="' + participant.find('input').val() + '"]').closest('.mb-3').remove();
        participant.remove();
    }

    Form.prototype.submit = function(evt) {
        evt.originalEvent.preventDefault();

        if (this.$.find('#new-invites-form').length) {
            this.$.find('[name="NewInvitesForm[invites][]"]').val().forEach(function(userGuid) {
                $('<input type="hidden" name="InvitesForm[invites][]" value="' + userGuid + '">')
                    .insertAfter(this.$.find('[name="InvitesForm[invites]"]'));
            }.bind(this));
        }

        client.submit(evt).then(function(response) {
            if (response.dataType === 'json' && response.data.next) {
                client.get(response.data.next).then(function (response) {
                    modal.global.setDialog(response);
                })
            } else if (response.dataType === 'json' && response.reloadWall) {
                modal.global.close(true);
                event.trigger('humhub:content:newEntry', response.output, this);
                event.trigger('humhub:content:afterSubmit', response.output, this);
            } else {
                modal.global.setDialog(response);
            }
        }).error(function() {
            modal.global.close();
            status.success(module.config.text.error.unsuccessful);
        })
    }

    const changeState = function(event) {
        client.post(event).then(function () {
            Widget.closest(event.$trigger).reload()
        }).catch(function (err) {
            module.log.error(err, true);
        });
    };

    module.export({
        WallEntry: WallEntry,
        Form: Form,
        changeState: changeState,
    });
});

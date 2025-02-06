humhub.module('letsMeet', function (module, require, $) {
    const Widget = require('ui.widget.Widget');
    const object = require('util.object');
    const client = require('client');
    const additions = require('ui.additions');
    const modal = require('ui.modal');
    const event = require('event');

    const WallEntry = function (node, options) {
        Widget.call(this, node, options);
    };

    object.inherits(WallEntry, Widget);

    WallEntry.prototype.init = function() {
        this.renderScrollButtons();
        $(window).resize(this.renderScrollButtons.bind(this));
    }

    WallEntry.prototype.renderScrollButtons = function() {
        const scrollableContainer = this.$.find('.scrollable-container').first();
        const scrollLeftButton = this.$.find('.scroll-left');
        const scrollRightButton = this.$.find('.scroll-right');

        if (scrollableContainer.width() < scrollableContainer[0].scrollWidth) {
            scrollLeftButton.show();
            scrollRightButton.show();
        } else {
            scrollLeftButton.hide();
            scrollRightButton.hide();
        }
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
            500
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

        const rowsContainer = $('#date-rows');

        client.post(
            event,
            {dataType: 'html', data: {index: rowsContainer.find('.date-row').length}}
        ).then(function (response) {
            rowsContainer.append(response.response);
            additions.applyTo(rowsContainer);
            renderButtons();
        })
    }

    Form.prototype.removeDateRow = function(event) {
        event.$target.closest('.date-row').remove();
        renderButtons();
    }

    Form.prototype.renderButtons = function() {
        const rowsContainer = $('#date-rows');

        rowsContainer.find('.add-row').hide();

        if (rowsContainer.find('.date-row').length === 1) {
            rowsContainer.find('.remove-row').hide();
        } else {
            rowsContainer.find('.remove-row').show();
        }

        rowsContainer.find('.add-row').last().show();
    }

    Form.prototype.inviteAllMembers = function(event) {
        const newInvitesForm = $('#new-invites-form');
        const invitesList = $('.invites');

        if (event.$target.prop('checked')) {
            newInvitesForm.hide();
            invitesList.hide();
        } else {
            newInvitesForm.show();
            invitesList.show();
        }
    }

    Form.prototype.removeParticipant = function(event) {
        const participant = event.$target.closest('li');

        $('input[value="' + participant.find('input').val() + '"]').closest('.form-group').remove();
        participant.remove();
    }

    Form.prototype.submit = function(evt) {
        evt.originalEvent.preventDefault();

        client.submit(evt).then(function(response) {
            if (response.dataType === 'json' && response.data.next) {
                client.get(response.data.next).then(function (response) {
                    modal.global.setDialog(response);
                })
            } else if (response.dataType === 'json' && response.reloadWall) {
                modal.global.close(true);
                event.trigger('humhub:content:newEntry', response.output, this);
                event.trigger('humhub:content:afterSubmit', response.output, this);
                module.log.success('success.saved');
            } else {
                modal.global.setDialog(response);
            }
        }).error(function() {
            modal.global.close();
            module.log.error('error.unsuccessful');
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
humhub.module('letsMeet', function (module, require, $) {
    const client = require('client');
    const additions = require('ui.additions');
    const modal = require('ui.modal');
    const event = require('event');


    const addDateRow = function (event) {
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

    const removeDateRow = function(event) {
        event.$target.closest('.date-row').remove();
        renderButtons();
    }

    const renderButtons = function() {
        const rowsContainer = $('#date-rows');

        rowsContainer.find('.add-row').hide();

        if (rowsContainer.find('.date-row').length === 1) {
            rowsContainer.find('.remove-row').hide();
        } else {
            rowsContainer.find('.remove-row').show();
        }

        rowsContainer.find('.add-row').last().show();
    }

    const inviteAllMembers = function(event) {
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
    
    const removeParticipant = function(event) {
        const participant = event.$target.closest('li');

        $('input[value="' + participant.find('input').val() + '"]').closest('.form-group').remove();
        participant.remove();
    }

    const loadTab = function(url) {

        client.get(url).then(function (response) {
            modal.global.setDialog(response);
        })
    }

    const submit = function(evt) {
        evt.originalEvent.preventDefault();

        client.submit(evt).then(function(response) {
            if (response.dataType === 'json' && response.data.next) {
                loadTab(response.data.next)
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

    module.export({
        addDateRow: addDateRow,
        removeDateRow: removeDateRow,
        submit: submit,
        inviteAllMembers: inviteAllMembers,
        removeParticipant: removeParticipant,
    });
});
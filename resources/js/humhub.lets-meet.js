humhub.module('letsMeet', function (module, require, $) {
    const object = require('util').object;
    const client = require('client');
    const Content = require('content').Content;
    const additions = require('ui.additions');
    const loader = require('ui.loader');
    const modal = require('ui.modal');


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
        rowsContainer.find('.remove-row').show();

        rowsContainer.find('.remove-row').last().hide();
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

        console.log(participant, $('input[value="' + participant.find('input').val() + '"]'))

        $('input[value="' + participant.find('input').val() + '"]').closest('.form-group').remove();
        participant.remove();
    }

    const loadTab = function(url) {

        client.get(url).then(function (response) {
            modal.global.setDialog(response);
            // modal.global.loader(false);
        })
    }

    const submit = function(event) {
        event.originalEvent.preventDefault();
        // modal.global.loader();

        client.submit(event).then(function(response) {

            console.log(response)

            if (response.dataType === 'json') {
                loadTab(response.data.next)
            } else {
                modal.global.setDialog(response);
                // changeModalButton()
            }
            // } else {
                // modal.global.close();
                // module.log.success('success.send');
            // }
        }).finally(function() {
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
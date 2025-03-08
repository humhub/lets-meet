<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace letsMeet;

use Yii;

class LetsMeetCest
{
    public function testCreateMeeting(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->wantToTest('the creation');
        $I->amGoingTo('install the module for space 3');
        $I->enableModule(3, 'lets-meet');
        $I->amOnSpace3();
        $I->click('#contentFormBody');
        $I->waitForElementVisible('#contentFormMenu');
        $I->click('Let\'s Meet');
        $I->waitForText('Create New Let\'s Meet');

        $I->see('Create New Let\'s Meet');
        $I->amGoingTo('Fill main info');
        $I->fillField('MainForm[title]', 'My Test Lets Meet Title');
        $I->fillField('#mainform-description .humhub-ui-richtext', 'My Test Lets Meet Description');
        $I->fillField('MainForm[duration]', '00:15');
        $I->checkOption('MainForm[make_public]');

        $I->clickNext();

        $I->amGoingTo('Fill days and times');
        $I->waitForText('Select dates for your poll');
        $I->see('Select dates for your poll');
        $tomorrow = new \DateTimeImmutable('tomorrow');
        $I->fillField('DayForm[0][day]', Yii::$app->formatter->asDate($tomorrow, 'short'));
        $I->selectFromPicker('#dayform-0-times', ['9:00 AM', '10:00 AM', '11:00 AM']);

        $I->click('.add-row', '#date-row-0');
        $I->waitForElementVisible('#dayform-1-day');
        $I->fillField('DayForm[1][day]', Yii::$app->formatter->asDate($tomorrow->modify('+1 day'), 'short'));
        $I->selectFromPicker('#dayform-1-times', ['12:00 PM', '1:00 PM', '2:00 PM']);

        $I->clickNext();

        $I->amGoingTo('invite users');
        $I->waitForText('Invite users');
        $I->see('Invite users');
        $I->checkOption('InvitesForm[invite_all_space_members]', '1');

        $I->clickSave();

        $I->expectTo('see the created lets meet in the space');
        $I->waitForText('My Test Lets Meet Title');

        $I->see('My Test Lets Meet Title');
    }

    public function testEditVote(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('edit my vote');
        $I->amGoingTo('vote on the in space 1');
        $I->amOnSpace3();

        $I->waitForText('Lets Meet Test Title');
        $I->see('Lets Meet Test Title');
        $I->click('Lets Meet Test Title');
        $I->wait(1);
        $I->waitForText('Lets Meet Test Description');
        $I->see('Lets Meet Test Description');
        $I->dontSee('Mar 19, 2035 at 2:00 PM with 2 votes.');

        $I->wait(1);
        $I->click('Edit Vote');
        $I->waitForText('Save Vote');

        $I->click(".times-container:nth-of-type(1) .expanded-vote:nth-of-type(1) .vote-decline");
        $I->click(".times-container:nth-of-type(1) .expanded-vote:nth-of-type(2) .vote-decline");
        $I->click(".times-container:nth-of-type(1) .expanded-vote:nth-of-type(3) .vote-decline");

        $I->click(".times-container:nth-of-type(2) .expanded-vote:nth-of-type(1) .vote-decline");
        $I->click(".times-container:nth-of-type(2) .expanded-vote:nth-of-type(2) .vote-decline");
        $I->click(".times-container:nth-of-type(2) .expanded-vote:nth-of-type(3) .vote-accept");

        $I->click('Save Vote');
        $I->waitForText('Edit Vote');

        $I->dontSee('Mar 18, 2035 at 9:00 AM with 2 votes.');
        $I->see('Mar 19, 2035 at 2:00 PM with 2 votes.');
    }

    public function testEditMeetingInfo(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('edit main info');
        $I->amOnSpace3();

        $I->clickEditFromContextMenu();

        $I->amGoingTo('edit title');
        $I->fillField('MainForm[title]', 'Lets Meet Changed Title');
        $I->amGoingTo('edit description');
        $I->fillField('#mainform-description .humhub-ui-richtext', 'Lets Meet Changed Description');

        $I->clickNext();
        $I->waitForText('Save');
        $I->clickSave();

        $I->waitForText('Lets Meet Changed Title');
        $I->see('Lets Meet Changed Title');
        $I->see('Lets Meet Changed Description');
    }

    public function testEditMeetingDuration(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('edit duration');
        $I->amOnSpace3();

        foreach ([
            '00:15' => '15 minutes',
            '01:30' => '1:30 hour',
            '03:00' => '3:00 hours',
        ] as $duration => $formattedDuration) {

            $I->amGoingTo("edit duration to $formattedDuration");
            $I->clickEditFromContextMenu();

            $I->fillField('MainForm[duration]', $duration);

            $I->clickNext();
            $I->waitForText('Select dates for your poll');
            $I->clickSave();

            $I->waitForText("Meeting Duration: $formattedDuration");
            $I->see("Meeting Duration: $formattedDuration");
        }
    }

    public function testEditMeetingDayAndTime(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('edit days');
        $I->amOnSpace3();

        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->see('Mar 19');
        $I->see('2:00 PM');

        $I->clickEditFromContextMenu();

        $I->clickNext();
        $I->waitForText('Select dates for your poll');
        $I->amGoingTo('change date');
        $I->fillField('DayForm[1][day]', Yii::$app->formatter->asDate((new \DateTime('3/19/35'))->modify('+1 day'), 'short'));
        $I->amGoingTo('change time');
        $I->selectFromPicker('#dayform-1-times', ['3:00 PM']);
        $I->clickSave();

        $I->waitForText('Mar 20');

        $I->expectTo('see that date and time are changed');
        $I->see('Mar 20');
        $I->see('3:00 PM');
    }

    public function testDeleteMeetingDay(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('delete day');
        $I->amOnSpace3();

        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->see('Mar 19');
        $I->see('2:00 PM');

        $I->clickEditFromContextMenu();

        $I->clickNext();
        $I->waitForText('Select dates for your poll');
        $I->amGoingTo('delete day');
        $I->click('.remove-row', '#date-row-1');

        $I->waitForText('Are you sure you want to delete this date?');
        $I->clickConfirm();
        $I->wait(1);

        $I->clickSave();
        $I->wait(1);

        $I->expectTo('Mar 19 is not visible anymore');
        $I->dontSee('Mar 19');
        $I->dontSee('2:00 PM');
    }

    public function testEditParticipants(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('edit participants and check permissions');
        $I->amOnSpace3();

        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->canSeeElement('.wall_humhubmodulesletsMeetmodelsMeeting_1 [data-original-title="Sara Tester"]');

        $I->wait(1);
        $I->click('.preferences .dropdown-toggle', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->wait(1);
        $I->waitForText('Participants');
        $I->click('Participants', '.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->waitForText('Let\'s Meet Participants');
        $I->see('Let\'s Meet Participants');

        $I->amGoingTo('disable invites for all space members');
        $I->uncheckOption('InvitesForm[invite_all_space_members]');
        $I->waitForElementVisible('#newinvitesform-invites');
        $I->amGoingTo('invite Peter Tester to vote');
        $I->selectUserFromPicker('#newinvitesform-invites', 'Peter Tester');

        $I->clickSave();

        $I->wait(1);
        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->wait(1);
        $I->click('.preferences .dropdown-toggle', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->wait(1);
        $I->waitForText('Participants');
        $I->click('Participants', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->waitForText('Let\'s Meet Participants');

        $I->cantSee('Sara Tester', '#invites');
        $I->canSee('Peter Tester', '#invites');

        $I->logout();
        $I->amUser2();
        $I->amOnSpace3();
        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->expectTo('see that Sara Tester can not vote');
        $I->dontSee('Save Vote', '.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->logout();
        $I->amUser1();
        $I->amOnSpace3();
        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->expectTo('see that Peter Tester can vote');
        $I->canSee('Save Vote', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
    }

    public function testCloseAndReopenMeeting(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('close and reopen meeting');
        $I->amOnSpace3();

        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->canSee('Edit Vote', '.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->amGoingTo('close meeting');
        $I->wait(1);
        $I->click('.preferences .dropdown-toggle', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->wait(1);
        $I->waitForText('Close');
        $I->click('Close', '.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->waitForText('Are you sure you want to close this Let\'s Meet?');
        $I->clickConfirm();
        $I->wait(1);
        $I->expectTo('see that Edit Vote button is not visible');
        $I->cantSee('Edit Vote', '.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->amGoingTo('reopen meeting');
        $I->wait(1);
        $I->click('.preferences .dropdown-toggle', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
        $I->wait(1);
        $I->waitForText('Reopen');
        $I->click('Reopen', '.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->waitForText('Are you sure you want to reopen this Let\'s Meet?');
        $I->clickConfirm();
        $I->wait(1);
        $I->expectTo('see that Edit Vote button is visible');
        $I->canSee('Edit Vote', '.wall_humhubmodulesletsMeetmodelsMeeting_1');
    }

    public function testScrollButtons(AcceptanceTester $I)
    {
        $I->amAdmin();
        $I->enableModule(3, 'lets-meet');
        $I->wantToTest('horizontal scroll buttons');
        $I->amOnSpace3();

        $I->waitForElementVisible('.wall_humhubmodulesletsMeetmodelsMeeting_1');

        $I->clickEditFromContextMenu();
        $I->clickNext();
        $I->waitForText('Select dates for your poll');
        $I->amGoingTo('add day');

        $I->click('.add-row', '#date-row-1');
        $I->waitForElementVisible('#dayform-2-day');
        $I->fillField('DayForm[2][day]', Yii::$app->formatter->asDate((new \DateTime('3/19/35'))->modify('+1 day'), 'short'));
        $I->selectFromPicker('#dayform-2-times', ['12:00 AM', '1:00 PM', '2:00 PM']);
        $I->clickSave();
        $I->waitForElementVisible('.scroll-left');
        $I->canSeeElement('.scroll-left', ['disabled' => '']);

        $I->amGoingTo('scroll right');
        $I->click('.scroll-right');
        $I->expectTo('see scroll right button is disabled');
        $I->canSeeElement('.scroll-right', ['disabled' => '']);

        $I->amGoingTo('scroll left');
        $I->click('.scroll-left');
        $I->expectTo('see scroll left button is disabled');
        $I->canSeeElement('.scroll-left', ['disabled' => '']);
    }
}

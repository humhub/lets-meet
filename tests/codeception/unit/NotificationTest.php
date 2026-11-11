<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\unit;

use humhub\modules\letsMeet\jobs\EveryoneVotedNotificationJob;
use humhub\modules\letsMeet\jobs\NewInviteNotificationJob;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingInvite;
use humhub\modules\letsMeet\notifications\EveryoneVotedNotification;
use humhub\modules\letsMeet\notifications\NewInviteNotification;
use humhub\modules\notification\components\NotificationManager;
use humhub\modules\notification\models\Notification;
use humhub\modules\space\models\Space;
use tests\codeception\_support\HumHubDbTestCase;

class NotificationTest extends HumHubDbTestCase
{
    public function testInviteAllSpaceUsers()
    {
        $this->becomeUser('Admin');
        $meeting = $this->createMeeting(true);

        (new NewInviteNotificationJob(['meetingId' => $meeting->id]))->run();

        // The members of the space are invited, the organizer is not notified
        $this->assertTrue($this->hasNotification(NewInviteNotification::class, $meeting, 2));
        $this->assertTrue($this->hasNotification(NewInviteNotification::class, $meeting, 3));
        $this->assertFalse($this->hasNotification(NewInviteNotification::class, $meeting, 1));
    }

    public function testInviteSelectedUsers()
    {
        $this->becomeUser('Admin');
        $meeting = $this->createMeeting(false);

        $invite = new MeetingInvite();
        $invite->meeting_id = $meeting->id;
        $invite->user_id = 3;
        $this->assertTrue($invite->save());

        (new NewInviteNotificationJob(['meetingId' => $meeting->id]))->run();

        $this->assertTrue($this->hasNotification(NewInviteNotification::class, $meeting, 3));
        $this->assertFalse($this->hasNotification(NewInviteNotification::class, $meeting, 2));
    }

    public function testInviteIsRendered()
    {
        $this->becomeUser('Admin');
        $meeting = $this->createMeeting(true);

        (new NewInviteNotificationJob(['meetingId' => $meeting->id]))->run();

        $record = Notification::findOne(['class' => NewInviteNotification::class, 'user_id' => 2]);
        $this->assertNotNull($record);

        $notification = NotificationManager::load($record);

        // The category keeps its id, so the settings of the users stay valid
        $this->assertSame('lets-meet', $notification::category()->id);

        // The sentences keep their wording; the emphasis comes from the parameters
        $web = $notification->asWeb();
        $this->assertStringContainsString('invites you to vote on', $web);
        $this->assertStringContainsString('<strong>Planning meeting</strong>', $web);
        $this->assertStringNotContainsString('<strong><strong>', $web);

        $subject = $notification->asMailSubject();
        $this->assertStringContainsString('Planning meeting', $subject);
        $this->assertStringContainsString("Meeting Let's Meet", $subject);
        $this->assertStringNotContainsString('<', $subject);
    }

    public function testEveryoneVotedIsSentToOrganizer()
    {
        $this->becomeUser('Admin');
        // Without invited users everybody has voted
        $meeting = $this->createMeeting(false);

        (new EveryoneVotedNotificationJob(['meetingId' => $meeting->id]))->run();

        $this->assertTrue($this->hasNotification(EveryoneVotedNotification::class, $meeting, 1));
        $this->assertFalse($this->hasNotification(EveryoneVotedNotification::class, $meeting, 2));

        // Sent once, whatever the participants do afterwards
        (new EveryoneVotedNotificationJob(['meetingId' => $meeting->id]))->run();
        $this->assertSame(1, (int) Notification::find()->where(['class' => EveryoneVotedNotification::class, 'user_id' => 1, 'content_id' => $meeting->content->id])->count());
    }

    private function createMeeting(bool $inviteAll): Meeting
    {
        $meeting = new Meeting(Space::findOne(4));
        $meeting->title = 'Planning meeting';
        $meeting->description = "Agenda:\nline two";
        $meeting->duration = '00:45:00';
        $meeting->invite_all_space_users = $inviteAll;
        $meeting->space_users_notified = false;
        $meeting->status = Meeting::STATUS_OPEN;
        $this->assertTrue($meeting->save());

        return $meeting;
    }

    private function hasNotification(string $class, Meeting $meeting, int $userId): bool
    {
        return Notification::find()
            ->where(['class' => $class, 'user_id' => $userId, 'content_id' => $meeting->content->id])
            ->exists();
    }
}

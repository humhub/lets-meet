<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\jobs;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingVote;
use humhub\modules\letsMeet\notifications\EveryoneVotedNotification;
use humhub\modules\queue\ActiveJob;
use yii\helpers\ArrayHelper;

class EveryoneVotedNotificationJob extends ActiveJob
{
    public int $meetingId;

    public function run()
    {
        /** @var Meeting $meeting */
        $meeting = Meeting::findOne(['id' => $this->meetingId]);

        if (!$meeting) {
            return;
        }

        if ($meeting->invite_all_space_users) {
            $userIds = $meeting->content->container->getMembershipUser()->select('user.id')->column();
        } else {
            $userIds = $meeting->getInvites()->select('user_id')->column();
        }

        $timeSlotCount = $meeting->getTimeSlots()->count();

        $meetingVotes = MeetingVote::find()
            ->where(['user_id' => $userIds])
            ->innerJoinWith(['timeSlot.day day' => function ($query) use ($meeting) {
                $query->andOnCondition(['day.meeting_id' => $meeting->id]);
            }], false)
            ->createCommand()
            ->queryAll();

        $meetingVotes = ArrayHelper::index($meetingVotes, 'time_slot_id', ['user_id']);

        if (count($meetingVotes) < count($userIds)) {
            return;
        }

        foreach ($meetingVotes as $votes) {
            if (count($votes) < $timeSlotCount) {
                return;
            }
        }

        EveryoneVotedNotification::instance()
            ->from($meeting->createdBy)
            ->about($meeting)
            ->send($meeting->createdBy);
    }
}

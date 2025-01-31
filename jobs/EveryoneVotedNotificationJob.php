<?php

namespace humhub\modules\letsMeet\jobs;

use humhub\modules\letsMeet\models\MeetingTimeSlot;
use humhub\modules\letsMeet\models\MeetingVote;
use humhub\modules\letsMeet\notifications\EveryoneVotedNotification;
use humhub\modules\queue\ActiveJob;
use yii\helpers\ArrayHelper;

class EveryoneVotedNotificationJob extends ActiveJob
{
    public $timeSlotId;

    public function run()
    {
        /** @var MeetingTimeSlot $timeSlot */
        $timeSlot = MeetingTimeSlot::find()->where(['id' => $this->timeSlotId])->one();

        if (!$timeSlot) {
            return;
        }

        $meeting = $timeSlot->day->meeting;

        if ($meeting->invite_all_space_users) {
            $userIds = $meeting->content->container->getMembershipUser()->select('user.id')->column();
        } else {
            $userIds = ArrayHelper::getColumn($meeting->invites, 'user.id');
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
            ->sendBulk($meeting->createdBy);
    }
}
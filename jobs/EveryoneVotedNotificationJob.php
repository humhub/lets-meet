<?php

namespace humhub\modules\letsMeet\jobs;

use humhub\modules\letsMeet\models\MeetingTimeSlot;
use humhub\modules\letsMeet\notifications\EveryoneVotedNotification;
use humhub\modules\queue\ActiveJob;
use yii\helpers\ArrayHelper;

class EveryoneVotedNotificationJob extends ActiveJob
{
    public $timeSlotId;

    public function run()
    {
        return;
        /** @var MeetingTimeSlot $timeSlot */
        $timeSlot = MeetingTimeSlot::find()->where(['id' => $this->timeSlotId])->one();

        if (!$timeSlot) {
            return;
        }

        $meeting = $timeSlot->day->meeting;

        if ($meeting->invite_all_space_users) {
            $userIds = $meeting->content->container->getMembershipUser()->select('id')->column();
        } else {
            $userIds = ArrayHelper::getColumn($meeting->invites, 'user.id');
        }

        $timeSlotCount = $meeting->getTimeSlots()->count();

        foreach ($this->model->daySlots as $daySlot) {
            foreach ($daySlot->timeSlots as $timeSlot) {
                $bestOptions[] = [
                    'acceptedVotes' => count($timeSlot->acceptedVotes),
                    'day' => $daySlot->date,
                    'time' => $timeSlot->time,
                ];
            }
        }


//        $timeSlot->day->meeting

        static::find()
            ->with('user')
            ->with('timeSlot.acceptedVotes')
            ->where(['user_id' => $votedUserIdsQuery->column()])
            ->orderBy(['user_id' => SORT_ASC])
            ->all();

        if (1) {

        }

        $userQuery = $this->content->container->getMembershipUser();
        if (!$this->invite_all_space_users) {
            $userQuery->andWhere(['user.id' => ArrayHelper::getColumn($this->invites, 'user.id')]);
        }

        EveryoneVotedNotification::instance()
            ->from($this->createdBy)
            ->about($this)
            ->sendBulk($userQuery);
    }
}
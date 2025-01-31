<?php

namespace humhub\modules\letsMeet\jobs;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\notifications\newInvite;
use humhub\modules\queue\ActiveJob;
use yii\helpers\ArrayHelper;

class NewInviteNotificationJob extends ActiveJob
{
    public int $meetingId;
    
    public function run()
    {
        $meeting = Meeting::findOne(['id' => $this->meetingId]);

        if (!$meeting) {
            return;
        }

        $userQuery = $meeting->content->container->getMembershipUser();
        if (!$meeting->invite_all_space_users) {
            $userQuery->andWhere(['user.id' => ArrayHelper::getColumn($meeting->invites, 'user.id')]);
        }

        NewInvite::instance()
            ->from($meeting->createdBy)
            ->about($this)
            ->sendBulk($userQuery);
    }
}
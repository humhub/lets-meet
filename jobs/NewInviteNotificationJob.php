<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\jobs;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingInvite;
use humhub\modules\letsMeet\notifications\NewInviteNotification;
use humhub\modules\queue\ActiveJob;
use Yii;

class NewInviteNotificationJob extends ActiveJob
{
    public int $meetingId;

    public function run()
    {
        /** @var Meeting $meeting */
        $meeting = Meeting::findOne(['id' => $this->meetingId]);

        if (!$meeting) {
            return;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $userQuery = $meeting->content->container->getMembershipUser();

            if ($meeting->invite_all_space_users && !$meeting->space_users_notified) {
                $meeting->space_users_notified = true;
                $meeting->save();
            } elseif (!$meeting->invite_all_space_users) {
                $invitedUserIds = $meeting->getInvites()->select('user_id')->andWhere(['notified' => 0])->column();
                MeetingInvite::updateAll(['notified' => true], ['user_id' => $invitedUserIds]);
                $userQuery->andWhere(['user.id' => $invitedUserIds]);
            } else {
                $transaction->rollBack();

                return;
            }

            NewInviteNotification::instance()
                ->from($meeting->createdBy)
                ->about($meeting)
                ->sendBulk($userQuery);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e);
            throw $e;
        }
    }
}

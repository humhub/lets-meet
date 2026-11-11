<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\notifications;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\notification\components\NotificationBlock;
use humhub\modules\notification\components\NotificationContext;
use Yii;

/**
 * The user was invited to vote on a meeting.
 */
final class NewInviteNotification extends AbstractLetsMeetNotification
{
    protected function getMessage(array $params): string
    {
        return Yii::t('LetsMeetModule.notification', "{organizer} invites you to vote on {meeting} Let’s Meet in the Space {space}.", self::sentenceParams($params));
    }

    protected function getMailSubject(array $params): string
    {
        return Yii::t('LetsMeetModule.notification', "{organizer} invites you to vote on {meeting} Meeting Let's Meet", $params);
    }

    /**
     * The meeting with its organizer and description; the core appends the "View online" button.
     */
    public function getBlocks(NotificationContext $context): array
    {
        $meeting = $this->getSubjectRecord();
        if (!$meeting instanceof Meeting) {
            return parent::getBlocks($context);
        }

        return [
            NotificationBlock::heading($meeting->getContentName() . ' ' . $meeting->title),
            NotificationBlock::text(Yii::t('LetsMeetModule.notification', 'Organizer') . ': ' . $this->originator?->displayName),
            NotificationBlock::text(Yii::t('LetsMeetModule.notification', 'Description') . ":\n" . $meeting->description),
        ];
    }
}

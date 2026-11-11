<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\notifications;

use Yii;

/**
 * All invited participants have voted: sent to the organizer of the meeting.
 */
final class EveryoneVotedNotification extends AbstractLetsMeetNotification
{
    protected function getMessage(array $params): string
    {
        return Yii::t('LetsMeetModule.notification', "All invited participants for the {meeting} Let’s Meet in the Space {space} have voted.", self::sentenceParams($params));
    }

    protected function getMailSubject(array $params): string
    {
        return Yii::t('LetsMeetModule.notification', "All invited participants have voted.");
    }
}

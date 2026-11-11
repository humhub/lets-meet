<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\notifications;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\notification\components\MessageParam;
use humhub\modules\notification\components\NotificationCategory;
use Yii;

/**
 * Base of the notifications about a meeting: the category and the parameters of the sentences.
 *
 * The sentences keep their `{organizer}`, `{meeting}` and `{space}` placeholders and wording; the
 * emphasis is made by the parameters, so the sentences carry no HTML.
 */
abstract class AbstractLetsMeetNotification extends BaseNotification
{
    public static function category(): NotificationCategory
    {
        return new NotificationCategory(
            'lets-meet',
            Yii::t('LetsMeetModule.notification', 'Let\'s Meet'),
            Yii::t('LetsMeetModule.notification', 'Receive Notifications when someone invites you to a vote.'),
        );
    }

    protected function getMessageParams(): array
    {
        $meeting = $this->getSubjectRecord();

        return [
            'organizer' => $this->originator !== null ? MessageParam::user($this->originator) : '',
            'meeting' => MessageParam::emphasis($meeting instanceof Meeting ? (string) $meeting->title : '', 100),
        ];
    }

    /**
     * The built-in `spaceName` of the core under the name of the sentences.
     *
     * @param array<string, string|int> $params
     * @return array<string, string|int>
     */
    protected static function sentenceParams(array $params): array
    {
        return $params + ['space' => $params['spaceName'] ?? ''];
    }
}

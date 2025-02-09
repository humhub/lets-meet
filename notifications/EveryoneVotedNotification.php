<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */


namespace humhub\modules\letsMeet\notifications;

use humhub\modules\comment\models\Comment;
use humhub\modules\comment\notifications\CommentNotificationCategory;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\notification\models\Notification;
use humhub\modules\user\models\User;
use humhub\modules\user\notifications\Mentioned;
use Yii;

class EveryoneVotedNotification extends BaseNotification
{
    /**
     * @var Meeting
     */
    public $source;

    public $suppressSendToOriginator = false;

    public $moduleId = 'lets-meet';

    public function category()
    {
        return new LetsMeetNotificationCategory();
    }

    public function getMailSubject()
    {
        return Yii::t('LetsMeetModule.notification', "All invited participants have voted.");
    }

    public function html()
    {
        return Yii::t('LetsMeetModule.notification', "All invited participants for the <strong>{meeting}</strong> Let’s Meet in the Space <strong>{space}</strong> have voted.", [
            'meeting' => $this->source->title,
            'space' => $this->source->content->container->name,
        ]);
    }
}

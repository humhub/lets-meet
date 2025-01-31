<?php

namespace humhub\modules\letsMeet\notifications;

use Yii;
use humhub\modules\notification\components\NotificationCategory;

class LetsMeetNotificationCategory extends NotificationCategory
{
    public $id = "lets-meet";

    public function getDescription()
    {
        return Yii::t('LetsMeetModule.notification', 'Receive Notifications when someone invites you to a vote.');
    }

    public function getTitle()
    {
        return Yii::t('LetsMeetModule.notification', 'Let\'s Meet');
    }
}

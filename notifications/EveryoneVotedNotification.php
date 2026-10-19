<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\notifications;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\notification\components\BaseNotification;
use humhub\helpers\Html;
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
            'meeting' => Html::encode($this->source->title),
            'space' => Html::encode($this->source->content->container->name),
        ]);
    }
}

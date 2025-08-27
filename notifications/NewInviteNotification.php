<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\notifications;

use Yii;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\notification\components\BaseNotification;

class NewInviteNotification extends BaseNotification
{
    /**
     * @var Meeting
     */
    public $source;

    /**
     * @inheritdoc
     */
    public $moduleId = 'lets-meet';

    /**
     * @inheritdoc
     */
    public $viewName = 'newInvite';

    /**
     * @inheritdoc
     */
    public function category()
    {
        return new LetsMeetNotificationCategory();
    }

    public function getMailSubject()
    {
        return Yii::t('LetsMeetModule.notification', "{organizer} invites you to vote on {meeting} Meeting Let's Meet", [
            'organizer' => $this->originator->displayName,
            'meeting' => $this->source->title,
        ]);
    }

    public function html()
    {
        return Yii::t('LetsMeetModule.notification', "<strong>{organizer}</strong> invites you to vote on <strong>{meeting}</strong> Let’s Meet in the Space <strong>{space}</strong>.", [
            'organizer' => $this->originator->displayName,
            'meeting' => $this->source->title,
            'space' => $this->source->content->container->name,
        ]);
    }
}

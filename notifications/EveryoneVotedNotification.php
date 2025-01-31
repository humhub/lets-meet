<?php


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
use yii\bootstrap\Html;

class EveryoneVotedNotification extends BaseNotification
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
    public $viewName = 'everyoneVoted';

    /**
     * @inheritdoc
     */
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
        return Yii::t('LetsMeetModule.notification', "All invited participants for the {meeting} Let's Meet in the {space} have voted.", [
            'meeting' => $this->source->title,
            'space' => $this->source->contentContainer->name,
        ]);
    }
}

<?php

use humhub\widgets\mails\MailContentEntry;
use yii\helpers\Url;


/**
 * @var yii\web\View $this
 * @var \humhub\modules\user\models\User $originator
 * @var \humhub\modules\letsMeet\models\Meeting $source
 * @var array $_params_
 */

?>
<?php $this->beginContent('@notification/views/layouts/mail.php', $_params_); ?>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="left">
        <tr>
            <td>
                <?=
                humhub\widgets\mails\MailHeadline::widget([
                    'level' => 3,
                    'text' => $source->getContentName(). ' ' . $source->title,
                    'style' => 'text-transform:capitalize;'
                ])
                ?>
            </td>
        </tr>
        <tr>
            <td>
                <strong><?= Yii::t('LetsMeetModule.notification', 'Organizer') ?>:</strong> <?= $originator->displayName ?>
                <br>
                <strong><?= Yii::t('LetsMeetModule.notification', 'Description') ?>:</strong>
                <br>
                <?= nl2br($source->description) ?>
            </td>
        </tr>
        <tr>
            <td height="10">
            </td>
        </tr>
        <tr>
            <td>
                <?=
                humhub\widgets\mails\MailButtonList::widget(['buttons' => [
                    humhub\widgets\mails\MailButton::widget([
                        'url' => Url::to(['/content/perma', 'id' => $source->content->id], true),
                        'text' => Yii::t('LetsMeetModule.notification', 'View Online')
                    ])
                ]]);
                ?>
            </td>
        </tr>
    </table>
<?php $this->endContent();

<?php


use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\models\forms\InvitesForm;
use humhub\modules\letsMeet\models\forms\NewInvitesForm;
use yii\widgets\ActiveForm;
use humhub\modules\letsMeet\assets\LetsMeetAsset;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\widgets\Button;
use humhub\modules\user\widgets\UserPickerField;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image;
use yii\helpers\Html;
use humhub\widgets\LinkPager;
use humhub\widgets\Pjax;

/**
 * @var ActiveForm $form
 * @var NewInvitesForm $newInvitesModel
 * @var InvitesForm $model
 * @var \yii\data\ActiveDataProvider $invitesDataProvider
 * @var User[] $invites
 * @var \yii\web\View $this
 * @var ContentContainerActiveRecord $contentContainer
 * @var string $searchUsersUrl
 */

LetsMeetAsset::register($this);

$invites = $invitesDataProvider->models;

$header = TabsStateManager::instance()->id
    ? Yii::t('LetsMeetModule.base', 'Let\'s Meet Participants')
    : Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')
;

?>




<?php ModalDialog::begin(['header' => $header]) ?>

<div class="modal-body meeting-edit-modal" data-ui-widget="letsMeet.Form" data-ui-init>
    <div class="form-heading">
        <h5><?= Yii::t('LetsMeetModule.base', 'Invite users') ?></h5>
        <div>
            <?= Yii::t('LetsMeetModule.base', 'Select either individual users to receive an invite, or select all Space members.') ?>
        </div>
    </div>
    <?php Pjax::begin(['enablePushState' => false, 'id' => 'invites']) ?>
        <?php $form = ActiveForm::begin([
            'id' => 'new-invites-form',
            'options' => [
                'data-pjax' => 1,
                'style' => ['display' => $model->invite_all_space_members ? 'none' : ''],
            ],
        ]) ?>
            <div class="row">
                <div class="col-md-10">
                    <?= UserPickerField::widget([
                        'model' => $newInvitesModel,
                        'attribute' => 'invites',
                        'placeholder' => Yii::t('LetsMeetModule.base', 'Add participants...'),
                        'options' => ['label' => false],
                        'url' => $searchUsersUrl,
                    ]) ?>
                </div>
                <div class="col-md-2 text-right">
                    <?= Button::info()
                        ->submit()
                        ->options(['name' => 'add'])
                        ->icon('send') ?>
                </div>
            </div>
        <div class="invites" style="<?= Html::cssStyleFromArray(['display' => $model->invite_all_space_members ? 'none' : '']) ?>">
            <ul class="media-list">
                <?php foreach ($invites as $index => $user) : ?>
                    <li>
                        <?= $form->field($newInvitesModel, "currentInvites[$index]")->hiddenInput()->label(false) ?>
                        <div class="media">
                            <a href="<?= $user->getUrl() ?>" data-modal-close="1" class="media-body">
                                <?= Image::widget([
                                    'user' => $user,
                                    'link' => false,
                                    'width' => 32,
                                    'htmlOptions' => ['class' => 'media-object'],
                                ]) ?>
                                <h4 class="media-heading"><?= Html::encode($user->displayName) ?></h4>
                                <h5><?= Html::encode($user->displayNameSub) ?></h5>
                            </a>
                            <div class="media-body">
                                <?= Button::danger()->sm()
                                    ->icon('remove')
                                    ->confirm(null, Yii::t('LetsMeetModule.base', 'Are you sure want to remove the participant?'))
                                    ->action('removeParticipant') ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="text-center">
                <?= LinkPager::widget([
                    'pagination' => $invitesDataProvider->pagination,
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>

        <?php $form = ActiveForm::begin([
            'id' => 'invites-form',
            'options' => [
                'data-pjax' => 0,
            ],
        ]) ?>

            <?= $form->field($model, "invites")->hiddenInput(['value' => ''])->label(false) ?>
            <?php foreach ($invites as $user) : ?>
                <?= $form->field($model, "invites[]")
                        ->hiddenInput(['value' => $user->guid])
                        ->label(false) ?>
                <?php endforeach; ?>
            <?= $form->field($model, 'invite_all_space_members')->checkbox(['data' => ['action-change' => 'inviteAllMembers']]) ?>

            <div class="text-center">
                <?php if (TabsStateManager::instance()->id): ?>
                    <?= ModalButton::cancel(); ?>
                <?php else: ?>
                    <?= ModalButton::defaultType('Previous')->load($contentContainer->createUrl('/lets-meet/index/dates', ['hash' => TabsStateManager::instance()->hash])); ?>
                <?php endif; ?>
                <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', 'Save'))->action('submit')->loader()?>
            </div>
        <?php ActiveForm::end() ?>
    <?php Pjax::end() ?>
</div>

<?php ModalDialog::end() ?>

<?php

use humhub\helpers\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\letsMeet\assets\LetsMeetAsset;
use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\models\forms\InvitesForm;
use humhub\modules\letsMeet\models\forms\NewInvitesForm;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\LinkPager;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use humhub\widgets\Pjax;
use yii\data\ActiveDataProvider;
use yii\web\View;

/**
 * @var ActiveForm $form
 * @var NewInvitesForm $newInvitesModel
 * @var InvitesForm $model
 * @var ActiveDataProvider $invitesDataProvider
 * @var User[] $invites
 * @var View $this
 * @var ContentContainerActiveRecord $contentContainer
 * @var string $searchUsersUrl
 */

LetsMeetAsset::register($this);

$invites = $invitesDataProvider->models;

$title = TabsStateManager::instance()->id
    ? Yii::t('LetsMeetModule.base', 'Let\'s Meet Participants')
    : Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')
;

?>

<?php Modal::beginDialog([
        'title' => $title,
        'bodyOptions' => ['class' => 'modal-body meeting-edit-modal', 'data-ui-widget' => 'letsMeet.Form', 'data-ui-init' => true],
    ]) ?>

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
                'class' => $model->invite_all_space_members ? 'd-none' : '',
            ],
        ]) ?>
            <div class="d-flex">
                <div class="flex-grow-1">
                    <?= UserPickerField::widget([
                        'model' => $newInvitesModel,
                        'attribute' => 'invites',
                        'placeholder' => Yii::t('LetsMeetModule.base', 'Add participants...'),
                        'options' => ['label' => false],
                        'url' => $searchUsersUrl,
                    ]) ?>
                </div>
                <div class="ms-1">
                    <?= Button::info()
                        ->submit()
                        ->options(['name' => 'add'])
                        ->icon('send') ?>
                </div>
            </div>
            <div class="invites" style="<?= Html::cssStyleFromArray(['class' => $model->invite_all_space_members ? 'd-none' : '']) ?>">
                <div class="hh-list">
                    <?php foreach ($invites as $index => $user) : ?>
                        <div class="hh-list-item">
                            <?= $form->field($newInvitesModel, "currentInvites[$index]")->hiddenInput()->label(false) ?>
                            <div class="d-flex mt-3">
                                <a href="<?= $user->getUrl() ?>" data-modal-close="1" class="flex-grow-1">
                                    <?= Image::widget([
                                        'user' => $user,
                                        'link' => false,
                                        'width' => 32,
                                        'htmlOptions' => ['class' => 'flex-shrink-0'],
                                    ]) ?>
                                    <h4 class="mt-0"><?= Html::encode($user->displayName) ?></h4>
                                    <h5><?= Html::encode($user->displayNameSub) ?></h5>
                                </a>
                                <div>
                                    <?= Button::danger()->sm()
                                        ->icon('remove')
                                        ->confirm(null, Yii::t('LetsMeetModule.base', 'Are you sure want to remove the participant?'))
                                        ->action('removeParticipant') ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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
            <?= $form->field($model, "invites")->hiddenInput(['value' => '', 'class' => $model->hasErrors('invites') ? 'is-invalid' : ''])->label(false) ?>
            <?php foreach ($invites as $user) : ?>
                <?= $form->field($model, "invites[]")
                        ->hiddenInput(['value' => $user->guid])
                        ->label(false) ?>
                <?php endforeach; ?>
            <?= $form->field($model, 'invite_all_space_members')->checkbox(['data' => ['action-change' => 'inviteAllMembers']]) ?>

            <div class="modal-body-footer">
                <?php if (TabsStateManager::instance()->id): ?>
                    <?= ModalButton::cancel() ?>
                <?php else: ?>
                    <?= ModalButton::light('Previous')->load($contentContainer->createUrl('/lets-meet/index/dates', ['hash' => TabsStateManager::instance()->hash])) ?>
                <?php endif; ?>
                <?= ModalButton::primary(Yii::t('LetsMeetModule.base', 'Save'))->submit()->action('submit')->loader(false) ?>
            </div>
        <?php ActiveForm::end() ?>
    <?php Pjax::end() ?>
<?php Modal::endDialog() ?>

<?php

use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\models\MeetingDaySlot;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\widgets\form\ActiveForm;
use humhub\modules\letsMeet\assets\LetsMeetAsset;
use humhub\widgets\modal\Modal;
use humhub\widgets\ModalButton;
use \yii\web\View;

/**
 * @var ActiveForm $form
 * @var DayForm[] $models
 * @var View $this
 * @var ContentContainerActiveRecord $contentContainer
 */

LetsMeetAsset::register($this);

$tabStateManager = TabsStateManager::instance();
$prevUrl = $contentContainer->createUrl(
    '/lets-meet/index/edit',
    $tabStateManager->id ? ['id' => $tabStateManager->id] : ['hash' => $tabStateManager->hash]
);

$header = TabsStateManager::instance()->id
    ? Yii::t('LetsMeetModule.base', 'Edit Let\'s Meet')
    : Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')
;

?>

<?php Modal::begin(['header' => $header]) ?>
    <div class="modal-body meeting-edit-modal" data-ui-widget="letsMeet.Form" data-ui-init>
        <div class="form-heading">
            <h5><?= Yii::t('LetsMeetModule.base', 'Select dates for your poll') ?></h5>
            <div>
                <?= Yii::t('LetsMeetModule.base', 'To schedule an event, provide at least two options, different time slots or days.') ?>
            </div>
        </div>
        <?php $form = ActiveForm::begin() ?>

        <div id="date-rows">
            <?php foreach ($models as $index => $day) : ?>
                <?= $this->render('date_row', [
                    'form' => $form,
                    'model' => $day,
                    'index' => $index,
                    'contentContainer' => $contentContainer,
                    'last' => $index === count($models) - 1,
                ]) ?>
            <?php endforeach; ?>

            <?php if (empty($models)) : ?>
                <?= $this->render('date_row', [
                    'form' => $form,
                    'model' => new DayForm(),
                    'index' => 0,
                    'contentContainer' => $contentContainer,
                    'last' => true,
                ]) ?>
            <?php endif; ?>

        </div>


        <div class="text-center">
            <?= ModalButton::light('Previous')->load($prevUrl); ?>
            <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', $tabStateManager->id ? 'Save' : 'Next'))
                ->action('submit')->loader(false)?>
        </div>

        <?php ActiveForm::end() ?>
    </div>

<?php Modal::end() ?>

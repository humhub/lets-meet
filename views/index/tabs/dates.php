<?php

use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\models\MeetingDaySlot;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\widgets\richtext\RichTextField;
use yii\widgets\ActiveForm;
use humhub\modules\letsMeet\assets\LetsMeetAsset;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;

/**
 * @var ActiveForm $form
 * @var DayForm[] $models
 * @var \yii\web\View $this
 * @var ContentContainerActiveRecord $contentContainer
 */

LetsMeetAsset::register($this);

$tabStateManager = TabsStateManager::instance();
$prevUrl = $contentContainer->createUrl(
    '/lets-meet/index/edit',
    $tabStateManager->id ? ['id' => $tabStateManager->id] : ['hash' => $tabStateManager->hash]
);

?>




<?php ModalDialog::begin(['header' => Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')]) ?>

    <div class="modal-body meeting-edit-modal">
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
            <?= ModalButton::defaultType('Previous')->load($prevUrl); ?>
            <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', $tabStateManager->id ? 'Save' : 'Next'))
                ->action('letsMeet.submit')->loader(false)?>
        </div>

        <?php ActiveForm::end() ?>
    </div>

<?php ModalDialog::end() ?>

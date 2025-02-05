<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\letsMeet\common\TabsStateManager;
use yii\widgets\ActiveForm;
use humhub\modules\letsMeet\assets\LetsMeetAsset;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\widgets\MaskedInput;

/**
 * @var ActiveForm $form
 * @var EditForm $model
 * @var \yii\web\View $this
 * @var ContentContainerActiveRecord $contentContainer
 */

LetsMeetAsset::register($this);

$header = TabsStateManager::instance()->id
    ? Yii::t('LetsMeetModule.base', 'Edit Let\'s Meet')
    : Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')
;

?>


<?php ModalDialog::begin(['header' => $header]) ?>

<div class="modal-body meeting-edit-modal" data-ui-widget="letsMeet.Form" data-ui-init>
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'title')->textInput(['autofocus' => true, 'placeholder' => $model->getAttributeLabel('title')]) ?>
    <?= $form->field($model, 'description')->widget(RichTextField::class, ['placeholder' => $model->getAttributeLabel('description')]) ?>
    <?= $form->field($model, 'duration')->widget(MaskedInput::class, [
        'mask' => '99:99',
        'options' => [
            'inputmode' => 'numeric',
            'placeholder' => 'HH:MM',
        ],
    ]) ?>
    <?= $form->field($model, 'make_public')->checkbox() ?>


    <div class="text-center">
        <?= ModalButton::cancel(); ?>
        <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', 'Next'))->action('submit')->loader(false)?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<?php ModalDialog::end() ?>


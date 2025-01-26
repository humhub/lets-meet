<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use yii\widgets\ActiveForm;
use humhub\modules\letsMeet\assets\LetsMeetAsset;
use humhub\widgets\ModalDialog;
use humhub\widgets\ModalButton;
use humhub\modules\content\components\ContentContainerActiveRecord;

/**
 * @var ActiveForm $form
 * @var EditForm $model
 * @var \yii\web\View $this
 * @var ContentContainerActiveRecord $contentContainer
 */

LetsMeetAsset::register($this);

?>


<?php ModalDialog::begin(['header' => Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')]) ?>

<div class="modal-body meeting-edit-modal">
    <?php $form = ActiveForm::begin() ?>

    <?= $form->field($model, 'title')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'description')->widget(RichTextField::class) ?>
    <?= $form->field($model, 'duration')->textInput() ?>
    <?= $form->field($model, 'make_public')->checkbox() ?>


    <div class="text-center">
        <?= ModalButton::cancel(); ?>
        <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', 'Next'))->action('letsMeet.submit')->loader(false)?>
    </div>

    <?php ActiveForm::end() ?>
</div>

<?php ModalDialog::end() ?>


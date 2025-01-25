<?php


use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use yii\helpers\ArrayHelper;

/**
 * @var MainForm $model
 * @var \yii\web\View $this
 */

$tabView = ArrayHelper::getValue([
        'tabs/main',
        'tabs/dates',
        'tabs/invites',
    ],
    $model->step - 1,
);

?>

<?php ModalDialog::begin(['header' => Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')]) ?>

    <div class="modal-body meeting-edit-modal" data-ui-widget="lets-meet.Form" data-ui-init>
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'step')->hiddenInput()->label(false) ?>
        <?= $this->renderAjax($tabView, ['form' => $form, 'model' => $model]) ?>

        <div class="text-center">
            <?= ModalButton::cancel(); ?>
            <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', 'Next'))?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php ModalDialog::end() ?>

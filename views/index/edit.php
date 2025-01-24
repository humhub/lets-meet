<?php


use Yii;
use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;

/**
 * @var MainForm $model
 * @var \yii\web\View $this
 */

//Assets::register($this);

$tabs = [
    1 => 'tabs/main',
    'tabs/dates',
    'tabs/invites',
];

?>

<?php ModalDialog::begin(['header' => Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')]) ?>

    <div class="modal-body meeting-edit-modal" data-ui-widget="lets-meet.Form" data-ui-init>
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'step')->hiddenInput()->label(false) ?>
        <?= $this->render($model->getTabView(), ['form' => $form, 'model' => $model]) ?>

        <div class="text-center">
            <?= ModalButton::cancel(); ?>
            <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', 'Next'))?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php ModalDialog::end() ?>

<?php


use humhub\modules\letsMeet\assets\FormAsset;
use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use yii\helpers\ArrayHelper;
use humhub\modules\content\components\ContentContainerActiveRecord;

/**
 * @var string $content
 * @var \yii\web\View $this
 * @var ContentContainerActiveRecord $contentContainer
 */

FormAsset::register($this);

?>

<?php ModalDialog::begin(['header' => Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')]) ?>

    <div class="modal-body meeting-edit-modal">
        <?= $content ?>
        <div class="text-center">
            <?= ModalButton::cancel(); ?>
            <?= ModalButton::submitModal(null, Yii::t('LetsMeetModule.base', 'Next'))->action('letsMeet.submit')->loader(false)?>
        </div>
    </div>

<?php ModalDialog::end() ?>

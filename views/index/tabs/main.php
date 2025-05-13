<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\widgets\form\ActiveForm;
use humhub\modules\letsMeet\assets\LetsMeetAsset;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;
use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\widgets\MaskedInput;
use yii\web\View;

/**
 * @var ActiveForm $form
 * @var EditForm $model
 * @var View $this
 * @var ContentContainerActiveRecord $contentContainer
 */

LetsMeetAsset::register($this);

$title = TabsStateManager::instance()->id
    ? Yii::t('LetsMeetModule.base', 'Edit Let\'s Meet')
    : Yii::t('LetsMeetModule.base', 'Create New Let\'s Meet')
;

?>

<?php $form = Modal::beginFormDialog([
        'title' => $title,
        'bodyOptions' => ['class' => 'modal-body meeting-edit-modal', 'data-ui-widget' => 'letsMeet.Form', 'data-ui-init' => true],
        'footer' => ModalButton::cancel() . ModalButton::primary(Yii::t('LetsMeetModule.base', 'Next'))->action('submit')->loader(false),
    ]) ?>

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
</div>

<?php Modal::endFormDialog() ?>


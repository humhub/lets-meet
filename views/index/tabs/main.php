<?php

/**
 * @var \yii\widgets\ActiveForm $form
 * @var EditForm $model
 * @var \yii\web\View $this
 */

use humhub\modules\content\widgets\richtext\RichTextField;

?>

<?= $form->field($model, 'title')->textInput(['autofocus' => true]) ?>

<?= $form->field($model, 'description')->widget(RichTextField::class) ?>

<?= $form->field($model, 'duration')->textInput() ?>
<?= $form->field($model, 'make_public')->checkbox() ?>
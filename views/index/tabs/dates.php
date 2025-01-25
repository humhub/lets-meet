<?php

/**
 * @var \yii\widgets\ActiveForm $form
 * @var \humhub\modules\letsMeet\models\forms\DatesForm $model
 * @var \yii\web\View $this
 */

use humhub\modules\letsMeet\models\MeetingDaySlot;

$dayModel = new MeetingDaySlot();

$this->registerJsVar('date_row_template', $this->render(
    'date_row',
    ['form' => $form, 'model' => $model, 'day' => $dayModel, 'index' => '__INDEX__'])
);

?>

<?php foreach ($model->dates as $index => $day) : ?>
    <?= $this->render('date_row', ['form' => $form, 'model' => $model, 'day' => $day, 'index' => $index]) ?>
<?php endforeach; ?>

<?php if (empty($model->dates)) : ?>
    <?= $this->render('date_row', ['form' => $form, 'model' => $model, 'day' => $dayModel, 'index' => 0]) ?>
<?php endif; ?>

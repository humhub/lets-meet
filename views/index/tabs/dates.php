<?php

use humhub\modules\ui\form\widgets\DatePicker;
use humhub\modules\ui\form\widgets\TimePicker;

/**
 * @var \yii\widgets\ActiveForm $form
 * @var \humhub\modules\letsMeet\models\forms\DatesForm $model
 * @var \yii\web\View $this
 */

?>

<?php foreach ($model->dates as $date) : ?>
//
<?php endforeach; ?>

<?= $form->field($model, 'date[]')
    ->widget(DatePicker::class)
    ->label(false) ?>

<?= $form->field($model, 'time[]')
    ->widget(TimePicker::class)
    ->label(false) ?>


8:26
7:02
4:45
4:37
8:49
5:08
7:33
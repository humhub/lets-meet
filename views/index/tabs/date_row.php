<?php

use humhub\modules\ui\form\widgets\DatePicker;
use humhub\modules\ui\form\widgets\TimePicker;

/**
 * @var \yii\widgets\ActiveForm $form
 * @var \humhub\modules\letsMeet\models\forms\DatesForm $model
 * @var \yii\web\View $this
 */

?>

<div>
    <div class="row">
        <div class="col-md-1">
            <i class="fa fa-calendar-clock-o"></i>
        </div>
        <div class="col-md-10">
            <?= $form->field($model, 'date[]')
                ->widget(DatePicker::class)
                ->label(false) ?>
        </div>
        <div class="col-md-1">
            close
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <i class="fa fa-clock-o"></i>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'time[]')
                ->widget(TimePicker::class)
                ->label(false) ?>
        </div>
    </div>
</div>

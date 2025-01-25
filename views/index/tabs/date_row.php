<?php

use humhub\modules\letsMeet\widgets\TimeSlotPicker;
use humhub\modules\ui\form\widgets\DatePicker;
use humhub\widgets\Button;

/**
 * @var \yii\widgets\ActiveForm $form
 * @var \humhub\modules\letsMeet\models\forms\DatesForm $model
 * @var \yii\web\View $this
 * @var int|string $index
 */

?>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-1 text-center">
                <i class="fa fa-calendar-check-o"></i>
            </div>
            <div class="col-md-9">
                <?= $form->field($model, "dates[date][$index]")
                    ->widget(DatePicker::class)
                    ->label(false) ?>
            </div>
            <div class="col-md-2 text-right">
                <?= Button::danger()
                    ->icon('fa-times')
//                    ->lg()
//                    ->action('task.list.edit', TaskListUrl::editTaskList($list))
//                    ->loader(false)
//                    ->cssClass('task-list-edit tt task-toggled-color')
                    ->options(['title' => Yii::t('LetsMeetModule.base', 'Delete Day')])
//                    ->visible($canManage) ?>
                <?= Button::primary()
                    ->icon('fa-plus')
//                    ->lg()
//                    ->action('task.list.edit', TaskListUrl::editTaskList($list))
//                    ->loader(false)
//                    ->cssClass('task-list-edit tt task-toggled-color')
                    ->options(['title' => Yii::t('LetsMeetModule.base', 'Delete Day')])
//                    ->visible($canManage) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1 text-center">
                <i class="fa fa-clock-o"></i>
            </div>
            <div class="col-md-11">
                <?= $form->field($model, "dates[date][$index][time][0]")
                    ->widget(TimeSlotPicker::class, ['options' => ['class' => 'vazgen']])
                    ->label(false) ?>
            </div>
        </div>
    </div>
</div>

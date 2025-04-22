<?php

use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\widgets\TimeSlotPicker;
use humhub\modules\ui\form\widgets\DatePicker;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Button;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\widgets\form\ActiveForm;
use humhub\modules\letsMeet\models\forms\DayForm;
use yii\web\View;

/**
 * @var ActiveForm $form
 * @var DayForm $model
 * @var View $this
 * @var int|string $index
 * @var bool $last
 * @var ContentContainerActiveRecord $contentContainer
 */

?>

<div class="date-row" id="date-row-<?= $index ?>">
    <div class="row">
        <div class="col-xs-2 col-md-1 row-icons">
            <?= Icon::get('calendar-check-o')->size(Icon::SIZE_LG) ?>
        </div>
        <div class="col-xs-5 col-md-6">
            <?= $form->field($model, "[$index]day")
                ->widget(DatePicker::class, [
                    'options' => [
                        'placeholder' => $model->getAttributeLabel('day')
                    ]
                ])
                ->label(false) ?>
        </div>
        <div class="col-xs-5 text-right">
            <?= Button::asLink()->danger()
                ->icon('fa-times')
                ->action('removeDateRow')
                ->confirm(
                    Yii::t('LetsMeetModule.base', 'Delete'),
                    Yii::t('LetsMeetModule.base', 'Are you sure you want to delete this date?'),
                )
                ->options([
                    'title' => Yii::t('LetsMeetModule.base', 'Add'),
                    'class' => 'remove-row',
                ])
            ?>
            <?= Button::asLink()->primary()
                ->icon('fa-plus')
                ->action('addDateRow', $contentContainer->createUrl('/lets-meet/index/add-date-row', ['hash' => TabsStateManager::instance()->hash]))
                ->options([
                    'title' => Yii::t('LetsMeetModule.base', 'Delete'),
                    'class' => 'add-row',
                    'style' => ['display' => $last ? '' : 'none']
                ])
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-2 col-md-1 row-icons">
            <?= Icon::get('clock-o')->size(Icon::SIZE_LG) ?>
        </div>
        <div class="col-xs-10 col-md-11">
            <?= $form->field($model, "[$index]times")
                ->widget(TimeSlotPicker::class, [
                    'options' => [
                        'placeholder' => $model->getAttributeLabel('times')
                    ]
                ])
                ->label(false) ?>
        </div>
    </div>
</div>

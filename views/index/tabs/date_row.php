<?php

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\widgets\TimeSlotPicker;
use humhub\modules\ui\form\widgets\DatePicker;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
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
        <div class="col-2 col-lg-1 row-icons">
            <?= Icon::get('calendar-check-o')->size(Icon::SIZE_LG) ?>
        </div>
        <div class="col-5 col-lg-6">
            <?= $form->field($model, "[$index]day")
                ->widget(DatePicker::class)
                ->label(false) ?>
        </div>
        <div class="col-5 text-end">
            <?= Button::danger()
                ->icon('times')
                ->action('removeDateRow')
                ->confirm(
                    Yii::t('LetsMeetModule.base', 'Delete'),
                    Yii::t('LetsMeetModule.base', 'Are you sure you want to delete this date?'),
                )
                ->tooltip(Yii::t('LetsMeetModule.base', 'Delete'))
                ->cssClass('remove-row') ?>
            <?= Button::primary()
                ->icon('plus')
                ->action('addDateRow', $contentContainer->createUrl('/lets-meet/index/add-date-row', ['hash' => TabsStateManager::instance()->hash]))
                ->tooltip(Yii::t('LetsMeetModule.base', 'Add'))
                ->cssClass('add-row' . ($last ? '' : ' d-none')) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-2 col-lg-1 row-icons">
            <?= Icon::get('clock-o')->size(Icon::SIZE_LG) ?>
        </div>
        <div class="col-10 col-lg-11">
            <?= $form->field($model, "[$index]times")
                ->widget(TimeSlotPicker::class, [
                    'options' => [
                        'placeholder' => $model->getAttributeLabel('times'),
                    ],
                ])
                ->label(false) ?>
        </div>
    </div>
</div>

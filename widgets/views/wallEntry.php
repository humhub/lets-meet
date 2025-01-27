<?php

use humhub\modules\letsMeet\assets\WallEntryAsset;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\Label;
use humhub\modules\content\widgets\richtext\RichText;
use yii\helpers\Html;
use yii\web\View;
use humhub\widgets\Pjax;
use humhub\widgets\Button;
use humhub\modules\user\widgets\Image;

/**
 * @var View $this
 * @var Meeting $meeting
 */

WallEntryAsset::register($this);

$color = 'var(--text-color-secondary)';
?>

<div class="media" style="margin-top:20px;" data-action-component="calendar.CalendarEntry">
    <div class="clearfix" style="margin-bottom:10px">
        <?php if (!empty($meeting->description)) : ?>
            <div data-ui-markdown data-ui-show-more>
                <?= RichText::output($meeting->description) ?>
            </div>
        <?php endif; ?>
        <div>
            <?= Yii::t(
                    'LetsMeetModule.base',
                    '<strong>Meeting Duration</strong>: {hours, plural, =1{# hour} other{# hours}}', ['hours' => $meeting->duration]
            ) ?>
        </div>
    </div>

    <div class="lets-meet-container">
        <div class="slots-container">
            <div class="icons-cell">
                <div>
                    <?= Icon::get('calendar')->size(Icon::SIZE_LG) ?>
                </div>
                <div>
                    <?= Icon::get('clock-o')->size(Icon::SIZE_LG) ?>
                </div>
            </div>
            <div class="dates-cell scrollable-container">
                <?php foreach ($meeting->daySlots as $daySlot): ?>
                    <div>
                        <div class="day-cell">
                            <?= Yii::$app->formatter->asDate($daySlot->date) ?>
                        </div>
                        <div class="times-container">
                            <?php foreach ($daySlot->timeSlots as $timeSlot): ?>
                                <div class="time-slot">
                                    <?= Yii::$app->formatter->asTime($timeSlot->time, 'short') ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="votes-container">
            <div class="icons-cell">
                <div>
                    <?= Image::widget([
                        'user' => Yii::$app->user->identity,
                        'link' => false,
                        'width' => 36,
                        'htmlOptions' => ['class' => 'media-object', 'title' => Html::encode(Yii::$app->user->identity->displayName)],
                    ]) ?>
                </div>
            </div>
            <div class="dates-cell scrollable-container">
                <?php foreach ($meeting->daySlots as $daySlot): ?>
                    <div class="times-container">
                        <?php foreach ($daySlot->timeSlots as $timeSlot): ?>
                            <div class="expanded-vote">
                                <div class="time-slot-vote vote-accept">
                                    <?= Icon::get('check-circle')->size(Icon::SIZE_LG) ?>
                                </div>
                                <div class="time-slot-vote vote-maybe">
                                    <?= Icon::get('circle')->size(Icon::SIZE_LG) ?>
                                </div>
                                <div class="time-slot-vote vote-decline">
                                    <?= Icon::get('times-circle')->size(Icon::SIZE_LG) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="controls-container">
            <div class="scroll-left">
                <?= Button::defaultType()->icon('arrow-left')->action('letsMeetWallEntry.scrollLeft')->loader(false) ?>
            </div>
            <div class="control-buttons">
                <?= Button::primary(Yii::t('LetsMeetModule.base', 'Save Vote'))->submit()
                    ->options(['name' => 'vote', 'disabled' => 'disabled'])  ?>
            </div>
            <div class="scroll-right">
                <?= Button::defaultType()->icon('arrow-right')->action('letsMeetWallEntry.scrollRight')->loader(false) ?>
            </div>
        </div>
    </div>


    <?php Pjax::begin(['enablePushState' => false]) ?>
    <?php Pjax::end() ?>
</div>

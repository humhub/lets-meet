<?php

use humhub\modules\letsMeet\assets\WallEntryAsset;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingVote;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\Label;
use humhub\modules\content\widgets\richtext\RichText;
use yii\helpers\Url;
use yii\web\View;
use humhub\widgets\Pjax;
use humhub\widgets\Button;
use humhub\modules\user\widgets\Image;
use yii\widgets\ActiveForm;

/**
 * @var View $this
 * @var Meeting $meeting
 * @var MeetingVote $userVotes
 * @var MeetingVote $votes
 * @var int $votedUsersCount
 * @var array $bestOptions
 * @var bool $canVote
 */

WallEntryAsset::register($this);

$voteModel = new MeetingVote();
$isClosed = $meeting->status == Meeting::STATUS_CLOSED;

?>

<style>
    .time-slot, .time-slot-vote {
        width: <?= Yii::$app->formatter->isShowMeridiem() ? '66' : '50' ?>px;
    }
</style>

<div class="media" style="margin-top:20px;">
    <div class="clearfix" style="margin-bottom:10px">
        <?php if (!empty($meeting->description)) : ?>
            <div data-ui-markdown data-ui-show-more>
                <?= RichText::output($meeting->description) ?>
            </div>
        <?php endif; ?>
        <br/>
        <div>
            <?= Yii::t(
                'LetsMeetModule.base',
                '<strong>Meeting Duration</strong>: {hours, plural, =1{# hour} other{# hours}}', ['hours' => $meeting->duration]
            ) ?>
        </div>
    </div>

    <div class="lets-meet-container <?= $isClosed ? 'voting-closed' : '' ?>">
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
        <?php Pjax::begin(['enablePushState' => false]) ?>
            <?php ActiveForm::begin(['options' => ['data' => ['pjax' => 1]]]) ?>
                <?= $this->render('votes_row', [
                    'meeting' => $meeting,
                    'canVote' => empty($userVotes) && $canVote,
                    'votes' => $userVotes,
                    'user' => Yii::$app->user->identity,
                ]) ?>
                <div class="controls-container">
                    <div class="scroll-left">
                        <?= Button::defaultType()->icon('arrow-left')->action('letsMeetWallEntry.scrollLeft')->loader(false) ?>
                    </div>
                    <div class="control-buttons">
                        <?php if ($canVote): ?>
                            <?php if (empty($userVotes)): ?>
                                <?= Button::primary(Yii::t('LetsMeetModule.base', 'Save Vote'))->submit()
                                    ->options(['name' => 'action', 'value' => 'vote', 'disabled' => 'disabled'])  ?>
                            <?php else: ?>
                                <?= Button::defaultType(Yii::t('LetsMeetModule.base', 'Reset Vote'))->submit()
                                    ->options(['name' => 'action', 'value' => 'reset']) ?>
                            <?php endif; ?>
                        <?php endif; ?>

                    </div>
                    <div class="scroll-right">
                        <?= Button::defaultType()->icon('arrow-right')->action('letsMeetWallEntry.scrollRight')->loader(false) ?>
                    </div>
                </div>

                <div class="results-container">
                    <?php foreach ($votes as $userId => $vote): ?>
                        <?= $this->render('votes_row', [
                            'meeting' => $meeting,
                            'canVote' => false,
                            'votes' => $vote,
                            'user' => $vote[0]->user,
                        ]) ?>
                    <?php endforeach; ?>
                    <?php if ($votedUsersCount > 2): ?>
                        <?php if (count($votes) != $votedUsersCount): ?>
                            <div>
                                <?= Button::defaultType(Yii::t(
                                    'LetsMeetModule.base', 'Show All ({count})',
                                    ['count' => $votedUsersCount]
                                ))
                                    ->link(Url::current(['showAll' => 1]))
                                    ->pjax()
                                    ->options(['class' => 'show-all-btn']) ?>
                            </div>
                        <?php else: ?>
                            <div>
                                <?= Button::defaultType(Yii::t(
                                    'LetsMeetModule.base', 'Collapse ({count})',
                                    ['count' => $votedUsersCount - 2]
                                ))
                                    ->link(Url::current(['showAll' => 0]))
                                    ->pjax()
                                    ->options(['class' => 'collapse-btn']) ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="totals-container">
                    <div class="icons-cell">
                        <?= Icon::get('line-chart')->size(Icon::SIZE_LG) ?>
                    </div>
                    <div class="dates-cell scrollable-container">
                        <?php foreach ($meeting->daySlots as $daySlot): ?>
                            <div>
                                <div class="times-container">
                                    <?php foreach ($daySlot->timeSlots as $timeSlot): ?>
                                        <div class="time-slot">
                                            <?= count($timeSlot->acceptedVotes) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($bestOptions)): ?>
                    <div class="best-options-container">
                        <strong>
                            <?= Yii::t(
                                'LetsMeetModule.base',
                                'Best {options, plural, =1{option} other{options}}', ['options' => count($bestOptions)]
                            ) ?>
                        </strong>
                        <?php foreach ($bestOptions as $option): ?>
                            <div>
                                <?= Yii::t(
                                    'LetsMeetModule.base',
                                    '{day} at {time} with {votes, plural, =1{# vote} other{# votes}}.', [
                                        'day' => Yii::$app->formatter->asDate($option['day']),
                                        'time' => Yii::$app->formatter->asTime($option['time'], 'short'),
                                        'votes' => $option['acceptedVotes']
                                    ]
                                ) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php ActiveForm::end() ?>
        <?php Pjax::end() ?>
    </div>
</div>

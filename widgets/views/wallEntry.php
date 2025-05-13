<?php

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingVote;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\content\widgets\richtext\RichText;
use yii\helpers\Url;
use yii\web\View;
use humhub\widgets\Pjax;
use humhub\widgets\bootstrap\Button;
use humhub\modules\user\widgets\Image;
use humhub\widgets\form\ActiveForm;

/**
 * @var View $this
 * @var Meeting $meeting
 * @var MeetingVote $userVotes
 * @var MeetingVote $votes
 * @var int $votedUsersCount
 * @var array $bestOptions
 * @var bool $canVote
 * @var bool $canEditVote
 * @var string $duration
 */

$voteModel = new MeetingVote();
$isClosed = $meeting->status == Meeting::STATUS_CLOSED;

?>

<style>
    .time-slot, .time-slot-vote {
        width: <?= Yii::$app->formatter->isShowMeridiem() ? '66' : '50' ?>px;
    }
</style>

<div class="mt-4">
    <div class="mb-3">
        <?php if (!empty($meeting->description)) : ?>
            <div data-ui-markdown data-ui-show-more>
                <?= RichText::output($meeting->description) ?>
            </div>
        <?php endif; ?>
        <br/>
        <div>
            <?= Yii::t(
                'LetsMeetModule.base',
                '<strong>Meeting Duration</strong>: {duration}',
                ['duration' => $duration]
            ) ?>
        </div>
    </div>

    <?php Pjax::begin(['enablePushState' => 0, 'id' => "lets_meet_wall_entry_$meeting->id"]) ?>
        <div class="lets-meet-container <?= $isClosed ? 'voting-closed' : '' ?>" data-ui-widget="letsMeet.WallEntry" data-ui-init>
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
                            <span class="tt" data-bs-toggle="tooltip" data-placement="top" title="<?= Yii::$app->formatter->asDate($daySlot->date, 'long') ?>">
                                <?= Yii::$app->formatter->asDate($daySlot->date, 'MMM d') ?>
                            </span>
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
        <?php ActiveForm::begin([
            'action' => $meeting->content->container->createUrl('/lets-meet/index/content', ['id' => $meeting->id]),
            'options' => ['data' => ['pjax' => 1]]]
        ) ?>
            <?php if ($canVote): ?>
                <?= $this->render('votes_row', [
                    'meeting' => $meeting,
                    'canVote' => $meeting->status != $meeting::STATUS_CLOSED && (empty($userVotes) || $canEditVote),
                    'votes' => $userVotes,
                    'user' => Yii::$app->user->identity,
                ]) ?>
            <?php endif; ?>
            <div class="controls-container">
                <div class="scroll-left">
                    <?= Button::light()->icon('arrow-left')->action('scrollLeft')->loader(false) ?>
                </div>
                <div class="control-buttons">
                    <?php if ($canVote && $meeting->status != $meeting::STATUS_CLOSED): ?>
                        <?php if (empty($userVotes) || $canEditVote): ?>
                            <?= Button::primary(Yii::t('LetsMeetModule.base', 'Save Vote'))->submit()
                                ->options(['name' => 'action', 'value' => 'vote', 'disabled' => !$canEditVote])  ?>
                        <?php else: ?>
                            <?= Button::light(Yii::t('LetsMeetModule.base', 'Edit Vote'))->submit()
                                ->options(['name' => 'action', 'value' => 'edit']) ?>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
                <div class="scroll-right">
                    <?= Button::light()->icon('arrow-right')->action('scrollRight')->loader(false) ?>
                </div>
            </div>

            <?php if (!empty($votes)): ?>
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
                            <?= Button::light(Yii::t(
                                'LetsMeetModule.base', 'Show All ({count})',
                                ['count' => $votedUsersCount]
                            ))
                                ->link(Url::current(['showAll' => 1]))
                                ->pjax()
                                ->options(['class' => 'show-all-btn']) ?>
                        </div>
                    <?php else: ?>
                        <div>
                            <?= Button::light(Yii::t(
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
            <?php endif; ?>

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
    </div>
    <?php Pjax::end() ?>
</div>

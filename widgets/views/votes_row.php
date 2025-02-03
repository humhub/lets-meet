<?php

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingVote;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\modules\user\widgets\Image;
use humhub\modules\user\models\User;

/**
 * @var Meeting $meeting
 * @var MeetingVote[] $votes
 * @var bool $canVote
 * @var User $user
 */

$voteModel = new MeetingVote();

$voteOptions = [
    MeetingVote::VOTE_ACCEPT => [
        'icon' => 'check-circle',
        'class' => 'vote-accept'
    ],
    MeetingVote::VOTE_MAYBE => [
        'icon' => 'circle',
        'class' => 'vote-maybe'
    ],
    MeetingVote::VOTE_DECLINE => [
        'icon' => 'times-circle',
        'class' => 'vote-decline'
    ]
];

$votes = ArrayHelper::index($votes, 'time_slot_id');

?>


<div class="votes-container">
    <div class="icons-cell">
        <?= Image::widget([
            'user' => $user,
            'link' => true,
            'linkOptions' => ['data' => ['pjax' => 0]],
            'hideOnlineStatus' => true,
            'showTooltip' => true,
            'width' => 36,
            'htmlOptions' => ['class' => 'media-object'],
        ]) ?>
    </div>
    <div class="dates-cell scrollable-container">
        <?php foreach ($meeting->daySlots as $daySlot): ?>
            <div class="times-container">
                <?php foreach ($daySlot->timeSlots as $timeSlot): ?>
                    <?php
                    /** @var MeetingVote $vote */
                    $vote = ArrayHelper::getValue($votes, $timeSlot->id);
                    ?>
                    <?php if ($canVote): ?>
                        <div class="expanded-vote <?= $vote?->vote ? 'voted' : '' ?>">
                            <?= Html::activeHiddenInput($voteModel, "[$timeSlot->id]time_slot_id", ['value' => $timeSlot->id]) ?>
                            <?= Html::activeHiddenInput($voteModel, "[$timeSlot->id]vote", ['value' => $vote?->vote, 'class' => 'vote-value']) ?>
                            <?php foreach ($voteOptions as $value => $options): ?>
                                    <div class="time-slot-vote <?= $options['class'] ?> <?= $vote?->vote === $value ? 'voted' : '' ?>" data-value="<?= $value ?>" data-action-click="letsMeetWallEntry.vote">
                                        <?= Icon::get($options['icon'])->size(Icon::SIZE_LG) ?>
                                    </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else:?>
                        <div class="time-slot-vote voted <?= $voteOptions[$vote?->vote]['class'] ?? '' ?>">
                            <?= $vote ? Icon::get($voteOptions[$vote?->vote]['icon'] ?? '')->size(Icon::SIZE_LG) : '' ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php

namespace humhub\modules\letsMeet\widgets;

use humhub\modules\letsMeet\assets\WallEntryAsset;
use humhub\modules\letsMeet\jobs\EveryoneVotedNotificationJob;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingDaySlot;
use humhub\modules\letsMeet\models\MeetingTimeSlot;
use humhub\modules\letsMeet\models\MeetingVote;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class WallEntryContent extends Widget
{
    public Meeting $model;

    public function init()
    {
        parent::init();
        WallEntryAsset::register($this->view);
    }

    public function run()
    {
        $action = Yii::$app->request->post('action');

        /** @var MeetingVote[] $voteModels */
        $voteModels = ArrayHelper::index(
            ArrayHelper::merge([], [], ...ArrayHelper::getColumn($this->model->daySlots,
                function(MeetingDaySlot $daySlot) {
                    return ArrayHelper::getColumn($daySlot->timeSlots, function(MeetingTimeSlot $timeSlot) {
                        return new MeetingVote([
                            'time_slot_id' => $timeSlot->id,
                            'user_id' => Yii::$app->user->id,
                        ]);
                    });
                })),
            'time_slot_id'
        );

        $votedUserIdsQuery = $this->model->getVotes()
            ->select('user_id')
            ->distinct()
            ->where(['<>', 'user_id', Yii::$app->user->id])
            ->limit(Yii::$app->request->get('showAll') ? null : 2);

        $votedUsersCount = (clone $votedUserIdsQuery)->limit(null)->count();
        $userVotes = $this->getUserVotes();
        $canEditVote = false;

        if (Yii::$app->request->isPost) {
            if ($action == 'vote') {
                if (MeetingVote::loadMultiple($voteModels, Yii::$app->request->post()) && MeetingVote::validateMultiple($voteModels)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        MeetingVote::deleteAll([
                            'user_id' => Yii::$app->user->id,
                            'time_slot_id' => $this->model->getTimeSlots()->select('id')->column()
                        ]);

                        foreach ($voteModels as $voteModel) {
                            $voteModel->save();
                            if ($voteModel->hasErrors()) {
                                throw new \RuntimeException(Html::errorSummary($voteModel));
                            }
                        }

                        Yii::$app->queue->push(new EveryoneVotedNotificationJob([
                            'meetingId' => $this->model->id,
                        ]));

                        $transaction->commit();
                    } catch (\Throwable $e) {
                        $transaction->rollBack();

                        throw $e;
                    }
                }
                $userVotes = $this->getUserVotes();
            } elseif ($action == 'edit') {
                $canEditVote = true;
            }
        }

        $votes = ArrayHelper::index(
            $this->model->getVotes()
                ->with('user')
                ->with('timeSlot.acceptedVotes')
                ->where(['user_id' => $votedUserIdsQuery->column()])
                ->orderBy(['user_id' => SORT_ASC])
                ->all(),
            null, 'user_id'
        );

        $canVote = (
            (
                $this->model->invite_all_space_users &&
                $this->model->content->container->getMembershipUser()->andWhere(['user.id' => Yii::$app->user->id])->exists()
            )
            || $this->model->getInvites()->andWhere(['user_id' => Yii::$app->user->id])->exists()
            || $this->model->created_by == Yii::$app->user->id
        );

        return $this->render('wallEntry', [
            'meeting' => $this->model,
            'userVotes' => $userVotes,
            'canVote' => $canVote,
            'canEditVote' => $canEditVote,
            'votes' =>  $votes,
            'votedUsersCount' =>  $votedUsersCount,
            'bestOptions' => $this->getBestOptions(),
            'user' => $this->model->content->createdBy,
            'duration' => $this->formatDuration(),
        ]);
    }

    private function getUserVotes()
    {
        return $this->model->getVotes()
            ->andWhere(['user_id' => Yii::$app->user->id])
            ->indexBy('time_slot_id')
            ->all();
    }

    private function getBestOptions()
    {
        $bestOptions = [];

        foreach ($this->model->daySlots as $daySlot) {
            foreach ($daySlot->timeSlots as $timeSlot) {
                $bestOptions[] = [
                    'acceptedVotes' => count($timeSlot->acceptedVotes),
                    'day' => $daySlot->date,
                    'time' => $timeSlot->time,
                ];
            }
        }

        usort($bestOptions, function($a, $b) {
            return $b['acceptedVotes'] <=> $a['acceptedVotes'];
        });

        $maxAcceptedVotes = max(array_map(function($option) {
            return $option['acceptedVotes'];
        }, $bestOptions));

        return array_filter($bestOptions, function($option) use ($maxAcceptedVotes) {
            return $option['acceptedVotes'] > 1 && $option['acceptedVotes'] === $maxAcceptedVotes;
        });
    }

    private function formatDuration() {
        $parts = explode(':', $this->model->duration);
        $hours = (int)$parts[0];
        $minutes = $parts[1];

        if ($hours === 0) {
            return Yii::t('LetsMeetModule.base', '{minutes, plural, =1{# minute} other{# minutes}}', ['minutes' => $minutes]);
        } elseif ($minutes === 0) {
            return Yii::t('LetsMeetModule.base', '{hours, plural, =1{# hour} other{# hours}}', ['hours' => $hours]);
        } else {
            return Yii::t('LetsMeetModule.base', '{hours}:{minutes} {hourLabel}', [
                'hours' => $hours,
                'minutes' => $minutes,
                'hourLabel' => Yii::t('LetsMeetModule.base', '{hours, plural, =1{hour} other{hours}}', ['hours' => $hours])
            ]);
        }
    }
}

<?php

namespace humhub\modules\letsMeet\widgets;

use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;
use humhub\modules\content\widgets\WallEntryControlLink;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingDaySlot;
use humhub\modules\letsMeet\models\MeetingTimeSlot;
use humhub\modules\letsMeet\models\MeetingVote;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property Meeting $model
 */
class WallEntry extends WallStreamModuleEntryWidget
{
    /**
     * Route to create a content
     *
     * @var string
     */
    public $createRoute = '/lets-meet/index/create';

    /**
     * @inheritDoc
     */
    public $editRoute = '/lets-meet/index/edit';

    public $editMode = self::EDIT_MODE_MODAL;

    public function init()
    {
        parent::init();

        if ($this->model->status == Meeting::STATUS_CLOSED) {
            $this->editRoute = false;
        }
    }

    public function getControlsMenuEntries()
    {
        $result = parent::getControlsMenuEntries();

        if (!$this->model->content->canEdit() || $this->model->status == Meeting::STATUS_CLOSED) {
            return $result;
        }


        $result[] = [
            WallEntryControlLink::class,
            [
                'label' => Yii::t('LetsMeetModule.base', 'Participants'),
                'icon' => 'fa-users',
                'action' => 'editModal',
                'options' => [
                    'data-action-url' => $this->model->content->container->createUrl('/lets-meet/index/invites', ['id' => $this->model->id]),
                ]
            ],
            [
               'sortOrder' => 100,
            ]
        ];

        $result[] = [
            WallEntryControlLink::class,
            [
                'label' => Yii::t('LetsMeetModule.base', 'Close'),
                'icon' => 'fa-times',
                'action' => 'letsMeetWallEntry.close',
                'options' => [
                    'data-action-url' => $this->model->content->container->createUrl('/lets-meet/index/close', ['id' => $this->model->id]),
                    'data-action-confirm-header' => Yii::t('LetsMeetModule.base', 'Close Let\'s Meet'),
                    'data-action-confirm' => Yii::t('LetsMeetModule.base', 'Are you sure you want to close this Let\'s Meet?'),
                ]
            ],
            [
               'sortOrder' => 200,
            ]
        ];

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function renderContent()
    {
        $action = Yii::$app->request->post('action');

        /** @var MeetingVote[] $voteModels */
        $voteModels = ArrayHelper::index(ArrayHelper::merge([], [], ...ArrayHelper::getColumn($this->model->daySlots, function(MeetingDaySlot $daySlot) {
            return ArrayHelper::getColumn($daySlot->timeSlots, function(MeetingTimeSlot $timeSlot) {
                return new MeetingVote([
                    'time_slot_id' => $timeSlot->id,
                    'user_id' => Yii::$app->user->id
                ]);
            });
        })), 'time_slot_id');

        $userVotes = $this->getUserVotes();

        $votedUserIdsQuery = $this->model->getVotes()
            ->select('user_id')
            ->distinct()
            ->where(['<>', 'user_id', Yii::$app->user->id])
            ->limit(Yii::$app->request->get('showAll') ? null : 2);

        $votedUsersCount = (clone $votedUserIdsQuery)->limit(null)->count();


        if (Yii::$app->request->isPost) {
            if ($action == 'vote' && empty($userVotes)) {
                if (MeetingVote::loadMultiple($voteModels, Yii::$app->request->post()) && MeetingVote::validateMultiple($voteModels)) {
                    foreach ($voteModels as $voteModel) {
                        $voteModel->save();
                    }
                }
                $userVotes = $this->getUserVotes();
            } elseif ($action == 'reset') {
                MeetingVote::deleteAll(['user_id' => Yii::$app->user->id, 'time_slot_id' => $this->model->getTimeSlots()->select('id')->column()]);
                $userVotes = [];
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

        $canVote = $this->model->status != $this->model::STATUS_CLOSED
            && (
                $this->model->invite_all_space_users
                || $this->model->getInvites()->andWhere(['user_id' => Yii::$app->user->id])->exists()
                || $this->model->created_by == Yii::$app->user->id
            );

        return $this->render('wallEntry', [
            'meeting' => $this->model,
            'userVotes' => $userVotes,
            'canVote' => $canVote,
            'votes' =>  $votes,
            'votedUsersCount' =>  $votedUsersCount,
            'voteModels' => $voteModels,
            'bestOptions' => $this->getBestOptions(),
            'user' => $this->model->content->createdBy,
            'contentContainer' => $this->model->content->container,
        ]);
    }

    private function getUserVotes()
    {
        return $this->model->getVotes()
            ->andWhere(['user_id' => Yii::$app->user->id])
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

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return $this->model->title;
    }
}

<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\common;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\letsMeet\jobs\NewInviteNotificationJob;
use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\models\forms\InvitesForm;
use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingDaySlot;
use humhub\modules\letsMeet\models\MeetingInvite;
use humhub\modules\letsMeet\models\MeetingTimeSlot;
use humhub\modules\content\models\Content;
use humhub\helpers\Html;
use Yii;
use yii\base\BaseObject;
use yii\base\StaticInstanceInterface;
use yii\base\StaticInstanceTrait;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * @property-read string $id
 * @property-read string $hash
 */
class TabsStateManager extends BaseObject implements StaticInstanceInterface
{
    use StaticInstanceTrait;

    private ?string $hash = null;
    private ?int $id = null;

    public function init()
    {
        parent::init();

        $this->hash = Yii::$app->security->generateRandomString();
    }

    private function setId($id): void
    {
        $this->id = $id;
    }

    public function restore($hash): void
    {
        $this->hash = $hash;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function saveState($for, $data, $id = null): ?Meeting
    {
        $this->setId($id);

        return $this->id ? $this->saveDatabaseState($for, $data) : $this->saveTempState($for, $data);
    }

    private function saveTempState($for, $data)
    {
        $state = $this->getState();
        ArrayHelper::setValue($state, $for, $data);
        Yii::$app->session->set($this->hash, $state);

        return null;
    }

    public function getState($for = null, $default = null, $id = null)
    {
        $this->setId($id);
        $state = $this->id ? $this->getDatabaseState($for) : $this->getTempState($for);

        return !empty($state) ? $state : $default;
    }

    private function getTempState($for = null)
    {
        $state = Yii::$app->session->get($this->hash);

        return $for ? ArrayHelper::getValue($state, $for) : $state;
    }

    private function saveDatabaseState($for, $data)
    {
        /** @var Meeting $meeting */
        $meeting = Meeting::findOne($this->id);

        match ($for) {
            MainForm::class => $this->saveMeeting($data, $meeting),
            DayForm::class => $this->saveDays($data, $meeting),
            InvitesForm::class => $this->saveInvites($data, $meeting),
            default => throw new \InvalidArgumentException("Unknown form type: $for"),
        };

        return $meeting;
    }

    private function getDatabaseState($for)
    {
        /** @var Meeting $meeting */
        $meeting = Meeting::find()->with([
            'daySlots',
            'daySlots.timeSlots',
            'invites',
            'invites.user',
        ])->where(['id' => $this->id])->one();

        if (!$meeting) {
            throw new NotFoundHttpException();
        }

        if ($for == MainForm::class) {
            return new MainForm([
                'title' => $meeting->title,
                'description' => $meeting->description,
                'duration' => $meeting->duration,
                'make_public' => $meeting->content->visibility == Content::VISIBILITY_PUBLIC,
            ]);
        } elseif ($for == DayForm::class) {
            $days = [];
            foreach ($meeting->daySlots as $day) {
                $days[] = new DayForm([
                    'day' => $day->date,
                    'times' => ArrayHelper::getColumn($day->timeSlots, fn(MeetingTimeSlot $timeSlot) => Yii::$app->formatter->asTime($timeSlot->time, 'short')),
                ]);
            }

            return $days;
        } elseif ($for == InvitesForm::class) {
            return new InvitesForm([
                'invites' => ArrayHelper::getColumn($meeting->invites, 'user.guid'),
                'invite_all_space_members' => $meeting->invite_all_space_users,
            ]);
        } else {
            throw new \InvalidArgumentException("Unknown form type: $for");
        }
    }

    public function saveFromTempState(ContentContainerActiveRecord $contentContainer)
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            /** @var MainForm $main */
            $main = $this->getState(MainForm::class);
            /** @var DayForm[] $days */
            $days = $this->getState(DayForm::class);
            /** @var InvitesForm $invites */
            $invites = $this->getState(InvitesForm::class);

            $meeting = new Meeting();

            $this->saveMeeting($main, $meeting, $contentContainer);
            $this->saveDays($days, $meeting);
            $this->saveInvites($invites, $meeting);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();

            Yii::error($e);

            return null;
        }

        return $meeting;
    }

    private function saveMeeting(MainForm $main, Meeting $meeting, ContentContainerActiveRecord $contentContainer = null)
    {
        if ($contentContainer) {
            $meeting->content->container = $contentContainer;
        }
        $meeting->content->visibility = $main->make_public ? Content::VISIBILITY_PUBLIC : Content::VISIBILITY_PRIVATE;
        $meeting->title = $main->title;
        $meeting->description = $main->description;
        $meeting->duration = $main->duration;

        if (!$meeting->save()) {
            throw new \RuntimeException(Html::errorSummary($meeting));
        }
    }

    private function saveDays(array $days, Meeting $meeting)
    {
        $existingDayIds = [];
        foreach ($days as $day) {
            $meetingDay = MeetingDaySlot::findOne(['meeting_id' => $meeting->id, 'date' => $day->day]) ?: new MeetingDaySlot();
            $meetingDay->meeting_id = $meeting->id;
            $meetingDay->date = $day->day;
            if (!$meetingDay->save()) {
                throw new \RuntimeException(Html::errorSummary($meetingDay));
            }
            $existingDayIds[] = $meetingDay->id;

            $existingTimeIds = [];
            foreach ($day->times as $time) {
                $time = Yii::$app->formatter->asTime($time, 'php:H:i');
                $meetingDayTime = MeetingTimeSlot::findOne(['day_id' => $meetingDay->id, 'time' => $time]) ?: new MeetingTimeSlot();
                $meetingDayTime->day_id = $meetingDay->id;
                $meetingDayTime->time = $time;
                if (!$meetingDayTime->save()) {
                    throw new \RuntimeException(Html::errorSummary($meetingDayTime));
                }
                $existingTimeIds[] = $meetingDayTime->id;
            }

            $existingTimes = MeetingTimeSlot::find()
                ->where(['day_id' => $meetingDay->id])
                ->andWhere(['not in', 'id', $existingTimeIds])
                ->all();

            foreach ($existingTimes as $time) {
                $time->delete();
            }
        }

        $existingDays = MeetingDaySlot::find()
            ->where(['meeting_id' => $meeting->id])
            ->andWhere(['not in', 'id', $existingDayIds])
            ->all();

        foreach ($existingDays as $day) {
            $day->delete();
        }
    }

    private function saveInvites(InvitesForm $invites, Meeting $meeting)
    {
        $meeting->invite_all_space_users = $invites->invite_all_space_members;
        $meeting->save();

        if ($invites->invite_all_space_members) {
            $invites->invites = [];
        }

        if (!empty($invites->invites) || $invites->invite_all_space_members) {
            MeetingInvite::deleteAll([
                'AND',
                ['=', 'meeting_id', $meeting->id],
                ['not in', 'user_id', $invites->userIds],
            ]);

            $currentInvites = $meeting->getInvites()->select('user_id')->column();

            foreach ($invites->userIds as $userId) {
                if (in_array($userId, $currentInvites)) {
                    continue;
                }

                $invite = new MeetingInvite();
                $invite->meeting_id = $meeting->id;
                $invite->user_id = $userId;
                if (!$invite->save()) {
                    throw new \RuntimeException(Html::errorSummary($invite));
                }
            }
        }

        Yii::$app->queue->push(new NewInviteNotificationJob([
            'meetingId' => $meeting->id,
        ]));
    }
}

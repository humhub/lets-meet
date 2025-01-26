<?php

namespace humhub\modules\letsMeet\common;

use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\models\forms\InvitesForm;
use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\models\MeetingDaySlot;
use humhub\modules\letsMeet\models\MeetingTimeSlot;
use Yii;
use yii\base\BaseObject;
use yii\base\StaticInstanceInterface;
use yii\base\StaticInstanceTrait;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property-read string $hash
 */
class TabsStateManager extends BaseObject implements StaticInstanceInterface
{
    use StaticInstanceTrait;

    private $hash;

    public function init()
    {
        parent::init();

        $this->hash = Yii::$app->security->generateRandomString();
    }

    public function restore($hash)
    {
        $this->hash = $hash;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function saveState($for, $data)
    {
        $state = $this->getState();
        ArrayHelper::setValue($state, $for, $data);
        Yii::$app->session->set($this->hash, $state);
    }

    public function getState($for = null, $default = null)
    {
        $state = Yii::$app->session->get($this->hash);
        $state = $for ? ArrayHelper::getValue($state, $for) : $state;

        return !empty($state) ? $state : $default;
    }

    public function save()
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
            $meeting->title = $main->title;
            $meeting->description = $main->description;
            $meeting->duration = (int)explode(':', $main->duration)[0];
            $meeting->is_public = $main->make_public;
            $meeting->invite_all_space_users = $invites->invite_all_space_members;
            if (!$meeting->save()) {
                throw new \RuntimeException(Html::errorSummary($meeting));
            }

            foreach ($days as $day) {
                $meetingDay = MeetingDaySlot::findOne(['meeting_id' => $meeting->id, 'date' => $day->day]) ?: new MeetingDaySlot();
                $meetingDay->meeting_id = $meeting->id;
                $meetingDay->date = $day->day;
                if (!$meetingDay->save()) {
                    throw new \RuntimeException(Html::errorSummary($meetingDay));
                }

                foreach ($day->times as $time) {
                    $meetingDayTime = MeetingTimeSlot::findOne(['day_id' => $meetingDay->id, 'time' => $time]) ?: new MeetingTimeSlot();
                    $meetingDayTime->day_id = $meetingDay->id;
                    $meetingDayTime->time = $time;
                    if (!$meetingDayTime->save()) {
                        throw new \RuntimeException(Html::errorSummary($meetingDayTime));
                    }
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();

            throw $e;

            return false;
        }

        return true;
    }
}
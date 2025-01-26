<?php

namespace humhub\modules\letsMeet\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $date
 * @property int $meeting_id
 * @property-read Meeting $meeting
 * @property-read MeetingTimeSlot[] $timeSlots
 */
class MeetingDaySlot extends ActiveRecord
{
    public static function tableName()
    {
        return 'lets_meet_meeting_day_slot';
    }

    public function rules()
    {
        return [
            [['date', 'meeting_id'], 'required'],
            [['date'], 'safe'],
            [['meeting_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('LetsMeetModule.base', 'ID'),
            'date' => Yii::t('LetsMeetModule.base', 'Date'),
            'meeting_id' => Yii::t('LetsMeetModule.base', 'Meeting ID'),
        ];
    }

    public function getMeeting()
    {
        return $this->hasOne(Meeting::class, ['id' => 'meeting_id']);
    }

    public function getTimeSlots()
    {
        return $this->hasMany(MeetingTimeSlot::class, ['day_id' => 'id']);
    }
}
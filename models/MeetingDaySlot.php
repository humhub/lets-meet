<?php

namespace humhub\modules\letsMeet\models;

use yii\db\ActiveRecord;

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
            'id' => 'ID',
            'date' => 'Date',
            'meeting_id' => 'Meeting ID',
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
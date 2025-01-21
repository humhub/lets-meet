<?php

namespace humhub\modules\letsMeet\models;

use yii\db\ActiveRecord;

/**
 * @property-read MeetingDaySlot $day
 * @property-read MeetingVote[] $votes
 */
class MeetingTimeSlot extends ActiveRecord
{
    public static function tableName()
    {
        return 'lets_meet_meeting_time_slot';
    }

    public function rules()
    {
        return [
            [['time', 'day_id'], 'required'],
            [['time'], 'safe'],
            [['day_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time' => 'Time',
            'day_id' => 'Day ID',
        ];
    }

    public function getDay()
    {
        return $this->hasOne(MeetingDaySlot::class, ['id' => 'day_id']);
    }

    public function getVotes()
    {
        return $this->hasMany(MeetingVote::class, ['time_slot_id' => 'id']);
    }
}
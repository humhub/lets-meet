<?php

namespace humhub\modules\letsMeet\models;

use yii\db\ActiveRecord;

class MeetingVote extends ActiveRecord
{
    public static function tableName()
    {
        return 'lets_meet_meeting_vote';
    }

    public function rules()
    {
        return [
            [['time_slot_id', 'user_id', 'vote'], 'required'],
            [['time_slot_id', 'user_id'], 'integer'],
            [['vote'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'time_slot_id' => 'Time Slot ID',
            'user_id' => 'User ID',
            'vote' => 'Vote',
        ];
    }

    public function getTimeSlot()
    {
        return $this->hasOne(MeetingTimeSlot::class, ['id' => 'time_slot_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
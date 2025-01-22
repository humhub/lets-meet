<?php

namespace humhub\modules\letsMeet\models;

use Yii;
use yii\db\ActiveRecord;
use humhub\modules\user\models\User;

/**
 * @property-read MeetingTimeSlot $timeSlot
 * @property-read User $user
 */
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
            'id' => Yii::t('LetsMeetModule.base', 'ID'),
            'time_slot_id' => Yii::t('LetsMeetModule.base', 'Time Slot ID'),
            'user_id' => Yii::t('LetsMeetModule.base', 'User ID'),
            'vote' => Yii::t('LetsMeetModule.base', 'Vote'),
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
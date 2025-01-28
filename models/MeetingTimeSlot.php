<?php

namespace humhub\modules\letsMeet\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $time
 * @property int $day_id
 * @property-read MeetingDaySlot $day
 * @property-read MeetingVote[] $votes
 * @property-read MeetingVote[] $acceptedVotes
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
            'id' => Yii::t('LetsMeetModule.base', 'ID'),
            'time' => Yii::t('LetsMeetModule.base', 'Time'),
            'day_id' => Yii::t('LetsMeetModule.base', 'Day ID'),
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
    public function getAcceptedVotes()
    {
        return $this->hasMany(MeetingVote::class, ['time_slot_id' => 'id'])->andWhere(['vote' => MeetingVote::VOTE_ACCEPT]);
    }
}
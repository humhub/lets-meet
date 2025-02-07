<?php

namespace humhub\modules\letsMeet\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use humhub\modules\user\models\User;

/**
 * @property int $meeting_id
 * @property int $user_id
 * @property bool $notified
 * @property-read Meeting $meeting
 * @property-read MeetingVote $votes
 * @property-read User $user
 */
class MeetingInvite extends ActiveRecord
{
    public static function tableName()
    {
        return 'lets_meet_meeting_invite';
    }

    public function rules()
    {
        return [
            [['meeting_id', 'user_id'], 'required'],
            [['meeting_id', 'user_id'], 'integer'],
            [['notified'], 'boolean'],
        ];
    }

    public function getMeeting() : ActiveQuery
    {
        return $this->hasOne(Meeting::class, ['id' => 'meeting_id']);
    }

    public function getUser() : ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getVotes() : ActiveQuery
    {
        return $this->hasMany(MeetingVote::class, ['user_id' => 'id']);
    }
}

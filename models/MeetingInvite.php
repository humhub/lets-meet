<?php

namespace humhub\modules\letsMeet\models;

use yii\db\ActiveRecord;
use humhub\modules\user\models\User;

/**
 * @property-read Meeting $meeting
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
            [['meeting_id', 'user_id', 'invite_status'], 'required'],
            [['meeting_id', 'user_id', 'invite_status'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'meeting_id' => 'Meeting ID',
            'user_id' => 'User ID',
            'invite_status' => 'Invite Status',
        ];
    }

    public function getMeeting()
    {
        return $this->hasOne(Meeting::class, ['id' => 'meeting_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\models;


use Yii;
use yii\db\ActiveRecord;
use humhub\modules\user\models\User;

/**
 * @property int $time_slot_id
 * @property int $user_id
 * @property bool $vote
 * @property-read MeetingTimeSlot $timeSlot
 * @property-read User $user
 */
class MeetingVote extends ActiveRecord
{
    const VOTE_DECLINE = 0;
    const VOTE_ACCEPT = 1;
    const VOTE_MAYBE = 2;

    public static function tableName()
    {
        return 'lets_meet_meeting_vote';
    }

    public function rules()
    {
        return [
            [['time_slot_id', 'user_id', 'vote'], 'required'],
            [['time_slot_id', 'user_id', 'vote'], 'integer'],
            ['vote', 'in', 'range' => [self::VOTE_DECLINE, self::VOTE_ACCEPT, self::VOTE_MAYBE]],

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

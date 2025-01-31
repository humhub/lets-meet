<?php

namespace humhub\modules\letsMeet\models;


use humhub\modules\letsMeet\jobs\NewInviteNotificationJob;
use humhub\modules\letsMeet\notifications\newInvite;
use Yii;
use humhub\modules\letsMeet\widgets\WallEntry;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\search\interfaces\Searchable;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $duration
 * @property bool $invite_all_space_users
 * @property int $status
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 * @property-read MeetingInvite[] $invites
 * @property-read MeetingDaySlot[] $daySlots
 * @property-read MeetingTimeSlot[] $timeSlots
 * @property-read MeetingVote[] $votes
 * @property-read User $createdBy
 * @property-read User $updatedBy
 */
class Meeting extends ContentActiveRecord implements Searchable
{
    const STATUS_OPEN = 1;
    const STATUS_CLOSED = 2;

    public $wallEntryClass = WallEntry::class;
    public $moduleId = 'lets-meet';

    public static function tableName()
    {
        return 'lets_meet_meeting';
    }

    public function rules()
    {
        return [
            [['title', 'description', 'duration'], 'required'],
            [['description'], 'string'],
            [['duration', 'status', 'created_by', 'updated_by'], 'integer'],
            [['invite_all_space_users'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('LetsMeetModule.base', 'ID'),
            'title' => Yii::t('LetsMeetModule.base', 'Title'),
            'description' => Yii::t('LetsMeetModule.base', 'Description'),
            'duration' => Yii::t('LetsMeetModule.base', 'Meeting duration'),
            'invite_all_space_users' => Yii::t('LetsMeetModule.base', 'Invite All Space Users'),
            'status' => Yii::t('LetsMeetModule.base', 'Status'),
            'created_at' => Yii::t('LetsMeetModule.base', 'Created At'),
            'created_by' => Yii::t('LetsMeetModule.base', 'Created By'),
            'updated_at' => Yii::t('LetsMeetModule.base', 'Updated At'),
            'updated_by' => Yii::t('LetsMeetModule.base', 'Updated By'),
        ];
    }

    public function getInvites() : ActiveQuery
    {
        return $this->hasMany(MeetingInvite::class, ['meeting_id' => 'id']);
    }

    public function getDaySlots() : ActiveQuery
    {
        return $this->hasMany(MeetingDaySlot::class, ['meeting_id' => 'id']);
    }

    public function getTimeSlots() : ActiveQuery
    {
        return $this->hasMany(MeetingTimeSlot::class, ['day_id' => 'id'])->via('daySlots');
    }

    public function getVotes() : ActiveQuery
    {
        return $this->hasMany(MeetingVote::class, ['time_slot_id' => 'id'])->via('timeSlots');
    }

    public function getCreatedBy() : ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy() : ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getIcon()
    {
        return 'fa-calendar-check-o';
    }

    public function getContentName()
    {
        return Yii::t('LetsMeetModule.base', 'Let\'s Meet');
    }

    public function getSearchAttributes()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            Yii::$app->queue->push(new NewInviteNotificationJob([
                'meetingId' => $this->id,
            ]));
        }
    }
}
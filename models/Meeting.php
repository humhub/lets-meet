<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\models;


use DateTime;
use Yii;
use yii\db\ActiveQuery;
use humhub\modules\letsMeet\widgets\WallEntry;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\search\interfaces\Searchable;
use humhub\modules\letsMeet\permissions\ManagePermission;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $duration
 * @property bool $invite_all_space_users
 * @property bool $space_users_notified
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

    protected $createPermission = ManagePermission::class;

    protected $managePermission = ManagePermission::class;

    public static function tableName()
    {
        return 'lets_meet_meeting';
    }

    public function getContentName()
    {
        return Yii::t('LetsMeetModule.base', 'Let\'s Meet');
    }

    public function getContentDescription()
    {
        return $this->title;
    }



    public function rules()
    {
        return [
            [['title', 'description', 'duration'], 'required'],
            [['description'], 'string'],
            [['status', 'created_by', 'updated_by'], 'integer'],
            [['duration'], 'time', 'format' => 'php:H:i:s'],
            [['invite_all_space_users'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
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

    public function getSearchAttributes()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function beforeValidate()
    {
        $this->normalizeDuration();

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        $this->normalizeDuration();

        return parent::beforeSave($insert);
    }

    private function normalizeDuration()
    {
        $this->duration = (new DateTime($this->duration))->format('H:i:s');
    }
}

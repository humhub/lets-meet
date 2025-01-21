<?php

namespace humhub\modules\letsMeet\models;

use yii\db\ActiveRecord;

class Meeting extends ActiveRecord
{
    public static function tableName()
    {
        return 'lets_meet_meeting';
    }

    public function rules()
    {
        return [
            [['title', 'description', 'duration', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'required'],
            [['description'], 'string'],
            [['duration', 'status', 'created_by', 'updated_by'], 'integer'],
            [['is_public', 'invite_all_space_users'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'duration' => 'Duration',
            'is_public' => 'Is Public',
            'invite_all_space_users' => 'Invite All Space Users',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function getInvites()
    {
        return $this->hasMany(MeetingInvite::class, ['meeting_id' => 'id']);
    }

    public function getDaySlots()
    {
        return $this->hasMany(MeetingDaySlot::class, ['meeting_id' => 'id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
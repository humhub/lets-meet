<?php

use yii\db\Migration;

class m250121_092111_initial extends Migration
{
    public function safeUp()
    {
        $this->createTable('lets_meet_meeting', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'duration' => $this->integer()->notNull(),
            'is_public' => $this->boolean()->notNull()->defaultValue(0),
            'invite_all_space_users' => $this->boolean()->notNull()->defaultValue(0),
            'status' => $this->integer(),
            'created_at' => $this->dateTime()->notNull(),
            'created_by' => $this->integer()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
            'updated_by' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_lets_meet_meeting_created_by', 'lets_meet_meeting', 'created_by', 'user', 'id');
        $this->addForeignKey('fk_lets_meet_meeting_updated_by', 'lets_meet_meeting', 'updated_by', 'user', 'id');

        $this->createTable('lets_meet_meeting_invite', [
            'id' => $this->primaryKey(),
            'meeting_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'invite_status' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_lets_meet_meeting_invite_meeting', 'lets_meet_meeting_invite', 'meeting_id', 'lets_meet_meeting', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('lets_meet_meeting_day_slot', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull(),
            'meeting_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_lets_meet_meeting_day_slot_meeting', 'lets_meet_meeting_day_slot', 'meeting_id', 'lets_meet_meeting', 'id', 'CASCADE', 'CASCADE');


        $this->createTable('lets_meet_meeting_time_slot', [
            'id' => $this->primaryKey(),
            'time' => $this->time()->notNull(),
            'day_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk_lets_meet_meeting_time_slot_day', 'lets_meet_meeting_time_slot', 'day_id', 'lets_meet_meeting_day_slot', 'id', 'CASCADE', 'CASCADE');


        $this->createTable('lets_meet_meeting_vote', [
            'id' => $this->primaryKey(),
            'time_slot_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'vote' => $this->boolean()->notNull(),
        ]);

        $this->addForeignKey('fk_lets_meet_meeting_time_slot_vote_time_slot', 'lets_meet_meeting_time_slot_vote', 'time_slot_id', 'lets_meet_meeting_time_slot', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_lets_meet_meeting_time_slot_vote_user', 'lets_meet_meeting_time_slot_vote', 'user_id', 'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        echo "m250121_092111_initial cannot be reverted.\n";

        return false;
    }
}

<?php

use yii\db\Migration;

class uninstall extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk_lets_meet_meeting_vote_user', 'lets_meet_meeting_vote');
        $this->dropForeignKey('fk_lets_meet_meeting_vote_time_slot', 'lets_meet_meeting_vote');
        $this->dropTable('lets_meet_meeting_vote');

        $this->dropForeignKey('fk_lets_meet_meeting_time_slot_day', 'lets_meet_meeting_time_slot');
        $this->dropTable('lets_meet_meeting_time_slot');

        $this->dropForeignKey('fk_lets_meet_meeting_day_slot_meeting', 'lets_meet_meeting_day_slot');
        $this->dropTable('lets_meet_meeting_day_slot');

        $this->dropForeignKey('fk_lets_meet_meeting_invite_user', 'lets_meet_meeting_invite');
        $this->dropForeignKey('fk_lets_meet_meeting_invite_meeting', 'lets_meet_meeting_invite');
        $this->dropTable('lets_meet_meeting_invite');

        $this->dropForeignKey('fk_lets_meet_meeting_updated_by', 'lets_meet_meeting');
        $this->dropForeignKey('fk_lets_meet_meeting_created_by', 'lets_meet_meeting');
        $this->dropTable('lets_meet_meeting');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}

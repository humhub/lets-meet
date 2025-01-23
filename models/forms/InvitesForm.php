<?php

namespace humhub\modules\letsMeet\models\forms;

use Yii;
use yii\helpers\ArrayHelper;

class InvitesForm extends Form
{
    public $invites;
    public $invite_all_space_members;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['invites', 'invite_all_space_members'], 'required'],
            [['invites'], 'each', 'rule' => ['integer']],
            [['invite_all_space_members'], 'boolean'],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'invites' => Yii::t('LetsMeetModule.base', 'Invites'),
            'invite_all_space_members' => Yii::t('LetsMeetModule.base', 'Invite all space members'),
        ];
    }
}
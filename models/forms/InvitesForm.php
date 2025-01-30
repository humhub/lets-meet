<?php

namespace humhub\modules\letsMeet\models\forms;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use humhub\modules\user\models\User;

/**
 * @property-read User[] $userIds
 */
class InvitesForm extends Model
{
    public $invites;
    public $invite_all_space_members;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['invites', 'invite_all_space_members'], 'required'],
            [['invites'], 'each', 'rule' => [
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'guid',
            ]],
            [['invite_all_space_members'], 'boolean'],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'invites' => Yii::t('LetsMeetModule.base', 'Participants'),
            'invite_all_space_members' => Yii::t('LetsMeetModule.base', 'Invite all Space members'),
        ];
    }

    public function getUserIds()
    {
        return User::find()->select('id')->where(['guid' => $this->invites])->column();
    }
}
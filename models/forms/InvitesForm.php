<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

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
            [['invites'], 'required', 'when' => [$this, 'isAllSpaceMembersNotInvited']],
            [['invites'], 'each', 'rule' => [
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'guid',
            ]],
            [['invite_all_space_members'], 'boolean'],
        ]);
    }

    public function isAllSpaceMembersNotInvited()
    {
        return !$this->invite_all_space_members;
    }

    public function attributeLabels()
    {
        return [
            'invites' => Yii::t('LetsMeetModule.form', 'Participants'),
            'invite_all_space_members' => Yii::t('LetsMeetModule.form', 'Invite all Space members'),
        ];
    }

    public function getUserIds()
    {
        return User::find()->select('id')->where(['guid' => $this->invites])->column();
    }
}

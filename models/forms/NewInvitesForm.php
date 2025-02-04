<?php

namespace humhub\modules\letsMeet\models\forms;

use humhub\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class NewInvitesForm extends Model
{
    public $invites;
    public $currentInvites;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['invites'], 'required'],
            [['invites', 'currentInvites'], 'each', 'rule' => [
                'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'guid',
            ]],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'invites' => Yii::t('LetsMeetModule.base', 'New Participant'),
        ];
    }
}

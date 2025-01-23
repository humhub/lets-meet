<?php

namespace humhub\modules\letsMeet\models\forms;

use Yii;
use yii\helpers\ArrayHelper;

class DatesForm extends Form
{
    public $dates;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['dates'], 'required'],
            [['dates'], 'each', 'rule' => [
                'date', 'format' => 'php:Y-m-d'
            ]],
            [['dates'], 'each', 'rule' => [
                'each', 'rule' => [
                    'required',
                    'time', 'format' => 'php:H:i'
                ]
            ]],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'dates' => Yii::t('LetsMeetModule.base', 'Dates'),
        ];
    }
}
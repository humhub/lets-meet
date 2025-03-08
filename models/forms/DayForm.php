<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\models\forms;

use humhub\libs\DbDateValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class DayForm extends Model
{
    public $id;
    public $day;
    public $times;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['id'], 'safe'],
            [['day', 'times'], 'required'],
            [['day'], DbDateValidator::class, 'convertToFormat' => 'Y-m-d'],
            [['times'], 'each', 'rule' => [
                'time', 'format' => Yii::$app->formatter->isShowMeridiem() ? 'h:mm a' : 'php:H:i',
            ]],
        ]);
    }

    public function attributeLabels()
    {
        return [
            'day' => Yii::t('LetsMeetModule.form', 'Day'),
            'times' => Yii::t('LetsMeetModule.form', 'Times'),
        ];
    }
}

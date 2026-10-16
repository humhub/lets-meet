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

    /**
     * Checks that the same day is not used in more than one row.
     *
     * Must be called after {@see Model::validateMultiple()}, because only then the days are
     * normalized to the `Y-m-d` format. Rows whose day already has an error are skipped.
     *
     * @param DayForm[] $models
     * @return bool true if there are no duplicated days
     */
    public static function validateUniqueDays(array $models): bool
    {
        $valid = true;
        $usedDays = [];

        foreach ($models as $model) {
            if ($model->hasErrors('day') || empty($model->day)) {
                continue;
            }

            if (isset($usedDays[$model->day])) {
                $model->addError('day', Yii::t('LetsMeetModule.form', 'This date is already used in another row.'));
                $valid = false;
            }

            $usedDays[$model->day] = true;
        }

        return $valid;
    }

    public function attributeLabels()
    {
        return [
            'day' => Yii::t('LetsMeetModule.form', 'Day'),
            'times' => Yii::t('LetsMeetModule.form', 'Times'),
        ];
    }
}

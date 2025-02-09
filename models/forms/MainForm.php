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

class MainForm extends Model
{
    public $title;
    public $description;
    public $duration;
    public $make_public;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['title', 'description', 'duration'], 'required'],
            [['description'], 'string'],
            [['duration'], 'time', 'format' => 'php:H:i'],
            [['make_public'], 'boolean'],
            [['title'], 'string', 'max' => 255],
        ]);
    }

    public function normalizeDuration()
    {
        if (is_numeric($this->duration)) {
            $this->duration = ($this->duration > 9 ? $this->duration : "0$this->duration") . ':00';
        }
    }

    public function attributeLabels()
    {
        return [
            'title' => Yii::t('LetsMeetModule.form', 'Title'),
            'description' => Yii::t('LetsMeetModule.form', 'Description'),
            'duration' => Yii::t('LetsMeetModule.form', 'Meeting duration'),
            'make_public' => Yii::t('LetsMeetModule.form', 'Make Public'),
        ];
    }
}

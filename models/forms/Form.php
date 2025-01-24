<?php

namespace humhub\modules\letsMeet\models\forms;

use yii\base\Model;

abstract class Form extends Model
{
    public $step = 1;

    public function rules()
    {
        return [
            [['step'], 'required'],
            [['step'], 'integer', 'min' => 1, 'max' => 3],
        ];
    }

    public function prev()
    {
        $this->step--;
    }

    public function next()
    {
        if ($this->validate()) {
            $this->step++;
            return $this->save();
        }

        return false;
    }

    public function save()
    {
        return true;
    }
}
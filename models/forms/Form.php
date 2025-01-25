<?php

namespace humhub\modules\letsMeet\models\forms;

use yii\base\Model;
use yii\helpers\ArrayHelper;

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

    /**
     * @param int $step
     * @return static
     */
    public static function getForm(int $step = 1)
    {
        /** @var static $form */
        $form = ArrayHelper::getValue([
            new MainForm(),
            new DatesForm(),
            new InvitesForm(),
        ], $step - 1);

        $form->step = $step;

        return $form;
    }
}
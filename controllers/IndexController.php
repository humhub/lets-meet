<?php

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\letsMeet\models\forms\Form;
use Yii;
use humhub\modules\content\components\ContentContainerController;

class IndexController extends ContentContainerController
{
    public function actionCreate()
    {
        return $this->actionEdit(null);
    }

    public function actionEdit($id)
    {
        $form = Form::getForm(2);


        if ($form->load(Yii::$app->request->post())) {
            if ($form->next()) {
                $form = Form::getForm($form->step);
            }
        }


        return $this->renderAjax('edit', ['model' => $form]);
    }
}

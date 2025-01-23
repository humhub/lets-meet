<?php

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\letsMeet\models\forms\MainForm;
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
        $form = new MainForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

        }

        return $this->renderAjax('edit', ['model' => $form]);
    }
}

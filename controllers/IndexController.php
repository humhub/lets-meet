<?php

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\letsMeet\models\forms\EditForm;
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
        $form = new EditForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {

        }

        return $this->renderAjax('edit', ['model' => $form]);
    }
}

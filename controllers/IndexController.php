<?php

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\letsMeet\models\forms\DatesForm;
use humhub\modules\letsMeet\models\forms\InvitesForm;
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
        $mainForm = new MainForm();
        $datesForm = new DatesForm();
        $invitesForm = new InvitesForm();

        if ($mainForm->load(Yii::$app->request->post())) {
            $mainForm->next();
        } elseif ($datesForm->load(Yii::$app->request->post())) {
            $datesForm->next();
        } elseif ($invitesForm->load(Yii::$app->request->post())) {
            $invitesForm->next();
        }

        return $this->renderAjax('edit', ['model' => $form]);
    }
}

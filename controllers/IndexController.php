<?php

namespace humhub\modules\letsMeet\controllers;

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
        return '';
    }
}

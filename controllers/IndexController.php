<?php

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\models\forms\Form;
use humhub\modules\letsMeet\models\forms\InvitesForm;
use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\modules\letsMeet\models\forms\NewInvitesForm;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\user\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\widgets\ActiveForm;
use humhub\modules\user\models\UserPicker;

class IndexController extends ContentContainerController
{
    public function actionCreate()
    {
        return $this->actionEdit(null);
    }

    public function actionEdit($id = null)
    {
        $model = new MainForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                return $this->asJson([
                    'next' => $this->contentContainer->createUrl('/lets-meet/index/dates')
                ]);
            }
        }

        return $this->renderAjax('tabs/main', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionDates($id = null)
    {
        $models = [];

        if (Yii::$app->request->isPost && $count = count(Yii::$app->request->post((new DayForm)->formName()))) {
            $models = array_fill(0, $count, new DayForm());
        }

        if (DayForm::loadMultiple($models, Yii::$app->request->post()) && DayForm::validateMultiple($models)) {
            return $this->asJson([
                'next' => $this->contentContainer->createUrl('/lets-meet/index/invites'),
            ]);
        }

        return $this->renderAjax('tabs/dates', [
            'models' => $models,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionInvites($id = null)
    {
        $newInvites = new NewInvitesForm();
        $model = new InvitesForm();

        if ($newInvites->load(Yii::$app->request->post()) && $newInvites->validate()) {
            $newInvites->currentInvites = ArrayHelper::merge($newInvites->invites ?: [], $newInvites->currentInvites ?: []);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                return $this->asJson([
                    'success' => true
                ]);
            }
        }

        $invitesDataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['guid' => $newInvites->currentInvites]),
            'pagination' => [
                'pageSize' => 100,
            ]
        ]);


        $newInvites->invites = null;

        return $this->renderAjax('tabs/invites', [
            'newInvitesModel' => $newInvites,
            'model' => $model,
            'invitesDataProvider' => $invitesDataProvider,
            'searchUsersUrl' => $this->contentContainer->createUrl('/lets-meet/index/search-participants'),
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionSearchParticipants(string $keyword, $id = null)
    {
        $filterParams = [
            'keyword' => $keyword,
            'fillUser' => true,
        ];

        return $this->asJson(UserPicker::filter($filterParams));
    }

    public function actionAddDateRow()
    {
        $model = new DayForm();
        $form = new ActiveForm();
        $index = Yii::$app->request->post('index', 0);

        return $this->renderAjax('tabs/date_row', [
            'form' => $form,
            'model' => $model,
            'last' => true,
            'index' => $index,
            'contentContainer' => $this->contentContainer,
        ]);
    }
}

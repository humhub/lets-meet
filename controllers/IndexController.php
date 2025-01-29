<?php

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\letsMeet\common\TabsStateManager;
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
use yii\widgets\ActiveForm;
use humhub\modules\user\models\UserPicker;

class IndexController extends ContentContainerController
{
    private ?TabsStateManager $stateManager;

    public function beforeAction($action)
    {
        $this->stateManager = TabsStateManager::instance();
        if ($hash = Yii::$app->request->get('hash')) {
            $this->stateManager->restore($hash);
        }

        return parent::beforeAction($action);
    }

    public function actionCreate()
    {
        return $this->actionEdit();
    }

    public function actionEdit($id = null)
    {
        $model = $this->stateManager->getState(MainForm::class, new MainForm(), $id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $this->stateManager->saveState(MainForm::class, $model, $id);

                return $this->asJson([
                    'next' => $this->contentContainer->createUrl(
                        '/lets-meet/index/dates',
                        $id ? ['id' => $id] : ['hash' => $this->stateManager->hash]
                    )
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
        $models = $this->stateManager->getState(DayForm::class, [new DayForm()], $id);

        if (Yii::$app->request->isPost) {
            $models = ArrayHelper::getColumn(Yii::$app->request->post((new DayForm)->formName()), function() {
                return new DayForm();
            });
        }

        if (DayForm::loadMultiple($models, Yii::$app->request->post()) && DayForm::validateMultiple($models)) {
            $this->stateManager->saveState(DayForm::class, $models, $id);

            if ($id) {
                return $this->asJson([]);
            }

            return $this->asJson([
                'next' => $this->contentContainer->createUrl(
                    '/lets-meet/index/invites',
                    ['hash' => $this->stateManager->hash]
                ),
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
        $model = $this->stateManager->getState(InvitesForm::class, new InvitesForm(), $id);

        if ($newInvites->load(Yii::$app->request->post())) {
            $model->invite_all_space_members = false;
            if ($newInvites->validate()) {
                $newInvites->currentInvites = ArrayHelper::merge($newInvites->invites ?: [], $newInvites->currentInvites ?: []);
            }
        } elseif (!Yii::$app->request->isPost) {
            $newInvites->currentInvites = $model->invites;
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->stateManager->saveState(InvitesForm::class, $model, $id);

            if (!$id) {
                $this->stateManager->saveFromTempState($this->contentContainer);
            }

            return $this->asJson([
                'success' => true
            ]);
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

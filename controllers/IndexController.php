<?php

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\models\forms\Form;
use humhub\modules\letsMeet\models\forms\InvitesForm;
use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\modules\letsMeet\models\forms\NewInvitesForm;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\permissions\ManagePermission;
use humhub\modules\letsMeet\widgets\WallEntryContent;
use humhub\modules\user\models\UserFilter;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\user\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;
use humhub\modules\user\models\UserPicker;
use humhub\modules\stream\actions\StreamEntryResponse;

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

    public function actionClose($id)
    {
        $meeting = $this->getMeeting($id);

        $meeting->status = Meeting::STATUS_CLOSED;
        $meeting->save();
    }

    public function actionReopen($id)
    {
        $meeting = $this->getMeeting($id);

        $meeting->status = Meeting::STATUS_OPEN;
        $meeting->save();
    }

    public function actionEdit($id = null)
    {
        if ($id) {
            $this->getMeeting($id);
        }

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
        if ($id) {
            $this->getMeeting($id);
        }

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
        if ($id) {
            $this->getMeeting($id);
        }

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
                $model = $this->stateManager->saveFromTempState($this->contentContainer);

                $entry = StreamEntryResponse::getAsArray($model->content);
                $entry['reloadWall'] = true;
                $entry['success'] = true;

                return $this->asJson($entry);
            }

            return $this->asJson([]);
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
        if (!$this->contentContainer->getPermissionManager()->can(new ManagePermission())) {
            throw new ForbiddenHttpException();
        }

        return $this->asJson(UserPicker::filter([
            'query' => $this->contentContainer->getMembershipUser()->andWhere(['<>', 'user.id', Yii::$app->user->id]),
            'keyword' => $keyword,
            'fillUser' => true,
            'fillQuery' => $this->contentContainer->getMembershipUser()->andWhere(['<>', 'user.id', Yii::$app->user->id]),
            'disabledText' => Yii::t(
                'SpaceModule.base',
                'This user is not a member of this space.',
            ),
        ]));
    }

    public function actionContent($id)
    {
        $meeting = Meeting::findOne(['id' => $id]);

        if (!$meeting) {
            throw new NotFoundHttpException();
        }

        if (!$meeting->content->canView()) {
            throw new ForbiddenHttpException();
        }

        return WallEntryContent::widget(['model' => $meeting]);
    }

    public function actionAddDateRow()
    {
        if (!$this->contentContainer->getPermissionManager()->can(new ManagePermission())) {
            throw new ForbiddenHttpException();
        }

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

    private function getMeeting(int $id): Meeting
    {
        $meeting = Meeting::findOne(['id' => $id]);

        if (!$meeting) {
            throw new NotFoundHttpException();
        }

        if (!$meeting->content->canEdit()) {
            throw new ForbiddenHttpException();
        }

        return $meeting;
    }
}

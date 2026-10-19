<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\letsMeet\common\TabsStateManager;
use humhub\modules\letsMeet\models\forms\DayForm;
use humhub\modules\letsMeet\models\forms\InvitesForm;
use humhub\modules\letsMeet\models\forms\MainForm;
use humhub\modules\letsMeet\models\forms\NewInvitesForm;
use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\permissions\ManagePermission;
use humhub\modules\letsMeet\widgets\WallEntryContent;
use humhub\modules\stream\actions\StreamEntryResponse;
use humhub\modules\user\models\User;
use humhub\modules\user\models\UserPicker;
use humhub\widgets\form\ActiveForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class IndexController extends ContentContainerController
{
    private ?TabsStateManager $stateManager = null;

    public function beforeAction($action)
    {
        $this->stateManager = TabsStateManager::instance();
        if ($hash = Yii::$app->request->get('hash')) {
            if (!is_string($hash) || !preg_match('/^[\w-]{32}$/', $hash)) {
                throw new BadRequestHttpException();
            }
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
        $this->forcePostRequest();

        $meeting = $this->getMeeting($id);

        $meeting->status = Meeting::STATUS_CLOSED;
        $meeting->save();

        $this->view->success(Yii::t('LetsMeetModule.base', 'Meeting successfully closed.'));
    }

    public function actionReopen($id)
    {
        $this->forcePostRequest();

        $meeting = $this->getMeeting($id);

        $meeting->status = Meeting::STATUS_OPEN;
        $meeting->save();

        $this->view->success(Yii::t('LetsMeetModule.base', 'Meeting successfully reopened.'));
    }

    public function actionEdit($id = null)
    {
        $this->checkAccess($id);

        $model = $this->stateManager->getState(MainForm::class, new MainForm(), $id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->stateManager->saveState(MainForm::class, $model, $id);

            return $this->asJson([
                'next' => $this->contentContainer->createUrl(
                    '/lets-meet/index/dates',
                    $id ? ['id' => $id] : ['hash' => $this->stateManager->hash],
                ),
            ]);
        }

        return $this->renderAjax('tabs/main', [
            'model' => $model,
            'contentContainer' => $this->contentContainer,
        ]);
    }

    public function actionDates($id = null)
    {
        $this->checkAccess($id);

        $models = $this->stateManager->getState(DayForm::class, [new DayForm()], $id);

        $postedDays = Yii::$app->request->post((new DayForm())->formName());
        if (Yii::$app->request->isPost && is_array($postedDays)) {
            $models = ArrayHelper::getColumn($postedDays, fn() => new DayForm());
        }

        if (DayForm::loadMultiple($models, Yii::$app->request->post()) && $this->validateDays($models)) {
            $this->stateManager->saveState(DayForm::class, $models, $id);

            if ($id) {
                return $this->view->saved();
            }

            return $this->asJson([
                'next' => $this->contentContainer->createUrl(
                    '/lets-meet/index/invites',
                    ['hash' => $this->stateManager->hash],
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
        $this->checkAccess($id);

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

            $this->view->saved();

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
            ],
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
        $meeting = $this->findMeeting($id);

        if (!$meeting->content->canView()) {
            throw new ForbiddenHttpException();
        }

        return WallEntryContent::widget(['model' => $meeting]);
    }

    public function actionAddDateRow()
    {
        $this->checkAccess();

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

    /**
     * @param DayForm[] $models
     */
    private function validateDays(array $models): bool
    {
        // Both validations must run to show all errors at once
        $valid = DayForm::validateMultiple($models);

        return DayForm::validateUniqueDays($models) && $valid;
    }

    /**
     * Checks the access to edit an existing meeting or to create a new one in the current container
     *
     * @param mixed $id
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    private function checkAccess($id = null): void
    {
        if ($id) {
            $this->getMeeting($id);
        } elseif (!(new Meeting($this->contentContainer))->content->canEdit()) {
            throw new ForbiddenHttpException();
        }
    }

    private function findMeeting($id): Meeting
    {
        $meeting = is_scalar($id) && ctype_digit((string)$id) ? Meeting::findOne(['id' => (int)$id]) : null;

        if (!$meeting || $meeting->content->contentcontainer_id != $this->contentContainer->contentcontainer_id) {
            throw new NotFoundHttpException();
        }

        return $meeting;
    }

    private function getMeeting($id): Meeting
    {
        $meeting = $this->findMeeting($id);

        if (!$meeting->content->canEdit()) {
            throw new ForbiddenHttpException();
        }

        return $meeting;
    }
}

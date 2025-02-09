<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\widgets;

use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;
use humhub\modules\content\widgets\WallEntryControlLink;
use humhub\modules\letsMeet\models\Meeting;
use Yii;

/**
 * @property Meeting $model
 */
class WallEntry extends WallStreamModuleEntryWidget
{
    /**
     * Route to create a content
     *
     * @var string
     */
    public $createRoute = '/lets-meet/index/create';

    /**
     * @inheritDoc
     */
    public $editRoute = '/lets-meet/index/edit';

    public $editMode = self::EDIT_MODE_MODAL;

    public function init()
    {
        parent::init();

        if ($this->model->status == Meeting::STATUS_CLOSED) {
            $this->editRoute = false;
        }
    }

    public function getControlsMenuEntries()
    {
        $result = parent::getControlsMenuEntries();

        if (!$this->model->content->canEdit()) {
            return $result;
        }

        if ($this->model->status === $this->model::STATUS_CLOSED) {
            $result[] = [
                WallEntryControlLink::class,
                [
                    'label' => Yii::t('LetsMeetModule.base', 'Reopen'),
                    'icon' => 'fa-check',
                    'action' => 'letsMeet.changeState',
                    'options' => [
                        'data-action-url' => $this->model->content->container->createUrl('/lets-meet/index/reopen', ['id' => $this->model->id]),
                        'data-action-confirm-header' => Yii::t('LetsMeetModule.base', 'Reopen Let\'s Meet'),
                        'data-action-confirm' => Yii::t('LetsMeetModule.base', 'Are you sure you want to reopen this Let\'s Meet?'),
                    ]
                ],
                [
                    'sortOrder' => 200,
                ]
            ];
        } else {
            $result[] = [
                WallEntryControlLink::class,
                [
                    'label' => Yii::t('LetsMeetModule.base', 'Participants'),
                    'icon' => 'fa-users',
                    'action' => 'editModal',
                    'options' => [
                        'data-action-url' => $this->model->content->container->createUrl('/lets-meet/index/invites', ['id' => $this->model->id]),
                    ]
                ],
                [
                    'sortOrder' => 100,
                ]
            ];

            $result[] = [
                WallEntryControlLink::class,
                [
                    'label' => Yii::t('LetsMeetModule.base', 'Close'),
                    'icon' => 'fa-times',
                    'action' => 'letsMeet.changeState',
                    'options' => [
                        'data-action-url' => $this->model->content->container->createUrl('/lets-meet/index/close', ['id' => $this->model->id]),
                        'data-action-confirm-header' => Yii::t('LetsMeetModule.base', 'Close Let\'s Meet'),
                        'data-action-confirm' => Yii::t('LetsMeetModule.base', 'Are you sure you want to close this Let\'s Meet?'),
                    ]
                ],
                [
                    'sortOrder' => 200,
                ]
            ];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function renderContent()
    {
        return WallEntryContent::widget(['model' => $this->model]);
    }

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return $this->model->title;
    }
}

<?php

namespace humhub\modules\letsMeet\widgets;

use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;

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
    public $editRoute = '/lets-meet/index/editt';

    public $editMode = self::EDIT_MODE_MODAL;

    public $createFormClass = WallCreateForm::class;

    /**
     * @inheritDoc
     */
    public function renderContent()
    {

        if ($this->model->closed) {
            $this->editRoute = '';
        }

        return $this->render('entry', ['lets-meet' => $this->model,
            'user' => $this->model->content->createdBy,
            'contentContainer' => $this->model->content->container]);
    }

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return 'Anasun!';
    }
}

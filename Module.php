<?php

namespace humhub\modules\letsMeet;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\letsMeet\permissions\ManagePermission;
use humhub\modules\space\models\Space;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use Yii;

class Module extends ContentContainerModule
{
    
    public $resourcesPath = 'resources';

    
    public function getContentContainerTypes()
    {
        return [
            Space::class,
        ];
    }

    protected function getAccessRules()
    {
        return [
            [
                'permission' => ManagePermission::class,
                'actions' => ['*'],
            ],
        ];
    }

    public function disable()
    {
        foreach (Meeting::find()->all() as $meeting) {
            $meeting->hardDelete();
        }

        parent::disable();
    }

    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        foreach (Meeting::find()->contentContainer($container)->all() as $meet) {
            $meet->hardDelete();
        }

        parent::disableContentContainer($container);
    }

    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer) {
            return [
                new ManagePermission(),
            ];
        }

        return [];
    }

    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return Yii::t('LetsMeetModule.base', 'Let\'s Meet');
    }

    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        return Yii::t('LetsMeetModule.base', 'Allows to start Let\'s Meet.');
    }

    public function getContentClasses(): array
    {
        return [Meeting::class];
    }

}

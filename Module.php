<?php

namespace humhub\modules\letsMeet;

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

    
    public function disable()
    {
        // delete all

        parent::disable();
    }

    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        foreach (Meet::find()->contentContainer($container)->all() as $meet) {
            $meet->hardDelete();
        }

        parent::disableContentContainer($container);
    }

    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer) {
            return [
                new permissions\CreateLetsMeet(),
            ];
        }

        return [];
    }

    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return Yii::t('LetsMeetModule.base', 'Lets Meet');
    }

    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        return Yii::t('LetsMeetModule.base', 'Allows to start lets Meet.');
    }

    public function getContentClasses(): array
    {
        return [Meet::class];
    }

}

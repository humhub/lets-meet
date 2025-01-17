<?php

namespace humhub\modules\letsMeet;

use humhub\modules\space\models\Space;
use Yii;
use yii\base\Event;

class Events
{
    public static function onWallEntryControlsInit($event)
    {
        $object = $event->sender->object;

        if(!$object instanceof Meet) {
            return;
        }
    }

    public static function onUserDelete($event)
    {
        foreach (Meet::findAll(array('created_by' => $event->sender->id)) as $meet) {
            $meet->delete();
        }

        return true;
    }

    public static function onIntegrityCheck($event)
    {

    }

    public static function onSampleDataInstall($event)
    {

    }

    public static function onRestApiAddRules()
    {
        /* @var \humhub\modules\rest\Module $restModule */
        $restModule = Yii::$app->getModule('rest');
//        $restModule->addRules([], 'letsMeet');
    }

}

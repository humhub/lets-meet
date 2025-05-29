<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet;

use humhub\modules\letsMeet\models\Meeting;
use humhub\modules\space\models\Space;
use Yii;

class Events
{
    public static function onWallEntryControlsInit($event)
    {
        $object = $event->sender->object;

        if (!$object instanceof Meeting) {
            return;
        }
    }

    public static function onUserDelete($event)
    {
        foreach (Meeting::findAll(['created_by' => $event->sender->id]) as $meet) {
            $meet->delete();
        }

        return true;
    }
}

<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\notification\models\Notification;
use yii\test\ActiveFixture;

class NotificationFixture extends ActiveFixture
{
    public $modelClass = Notification::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/Notification.php';
    public $depends = [];
}

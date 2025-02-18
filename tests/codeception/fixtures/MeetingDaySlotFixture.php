<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\letsMeet\models\MeetingDaySlot;
use yii\test\ActiveFixture;

class MeetingDaySlotFixture extends ActiveFixture
{
    public $modelClass = MeetingDaySlot::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/MeetingDaySlot.php';
    public $depends = [
        MeetingFixture::class,
    ];
}

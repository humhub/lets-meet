<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\letsMeet\models\MeetingTimeSlot;
use yii\test\ActiveFixture;

class MeetingTimeSlotFixture extends ActiveFixture
{
    public $modelClass = MeetingTimeSlot::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/MeetingTimeSlot.php';
    public $depends = [
        MeetingDaySlotFixture::class,
        MeetingVoteFixture::class,
    ];
}

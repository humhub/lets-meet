<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\letsMeet\models\Meeting;
use yii\test\ActiveFixture;

class MeetingFixture extends ActiveFixture
{
    public $modelClass = Meeting::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/Meeting.php';
    public $depends = [
        MeetingInviteFixture::class,
        MeetingTimeSlotFixture::class,
        ActivityFixture::class,
        ContentFixture::class,
        CommentFixture::class,
        NotificationFixture::class,
    ];
}

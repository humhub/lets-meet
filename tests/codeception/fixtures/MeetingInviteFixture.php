<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\letsMeet\models\MeetingInvite;
use humhub\modules\user\tests\codeception\fixtures\UserFixture;
use yii\test\ActiveFixture;

class MeetingInviteFixture extends ActiveFixture
{
    public $modelClass = MeetingInvite::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/MeetingInvite.php';
    public $depends = [
        MeetingFixture::class,
        UserFixture::class,
    ];
}

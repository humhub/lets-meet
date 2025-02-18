<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2025 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\letsMeet\models\MeetingVote;
use humhub\modules\user\tests\codeception\fixtures\UserFixture;
use yii\test\ActiveFixture;

class MeetingVoteFixture extends ActiveFixture
{
    public $modelClass = MeetingVote::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/MeetingVote.php';
    public $depends = [
        MeetingInviteFixture::class,
        UserFixture::class,
    ];
}

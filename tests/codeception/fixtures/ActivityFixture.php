<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\activity\models\Activity;;
use yii\test\ActiveFixture;

class ActivityFixture extends ActiveFixture
{
    public $modelClass = Activity::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/Activity.php';
    public $depends = [];
}

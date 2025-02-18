<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\content\models\Content;
use yii\test\ActiveFixture;

class ContentFixture extends ActiveFixture
{
    public $modelClass = Content::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/Content.php';
    public $depends = [];
}

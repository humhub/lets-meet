<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\letsMeet\tests\codeception\fixtures;

use humhub\modules\comment\models\Comment;
use yii\test\ActiveFixture;

class CommentFixture extends ActiveFixture
{
    public $modelClass = Comment::class;
    public $dataFile = '@lets-meet/tests/codeception/fixtures/data/Comment.php';
    public $depends = [
        ActivityFixture::class,
    ];
}
